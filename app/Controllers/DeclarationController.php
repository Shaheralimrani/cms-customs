<?php
declare(strict_types=1);

class DeclarationController
{
    private PDO $pdo;
    private DeclarationValidator $validator;
    private FileUploader $uploader;
    private DeclarationNumberGenerator $declarationNumberGenerator;
    private LicenseNumberGenerator $licenseNumberGenerator;
    private ImporterBroker $importerBroker;
    private DeclarationService $declarationService;

    public function __construct(
        PDO $pdo,
        DeclarationValidator $validator,
        FileUploader $uploader,
        DeclarationNumberGenerator $declarationNumberGenerator,
        LicenseNumberGenerator $licenseNumberGenerator,
        ImporterBroker $importerBroker,
        DeclarationService $declarationService
    ) {
        $this->pdo = $pdo;
        $this->validator = $validator;
        $this->uploader = $uploader;
        $this->declarationNumberGenerator = $declarationNumberGenerator;
        $this->licenseNumberGenerator = $licenseNumberGenerator;
        $this->importerBroker = $importerBroker;
        $this->declarationService = $declarationService;
    }

    public function process(array $post, array $files): array
    {
        $uploadedPaths = [];

        $validation = $this->validator->validate($post);

        if (!empty($validation['errors'])) {
            return [
                'success' => false,
                'errors' => $validation['errors'],
            ];
        }

        $data = $validation['data'];
        $items = $validation['items'];

        try {
            $fileFields = [
                'invoice_file',
                'origin_cert_file',
                'health_cert_file',
            ];

            $uploadedFiles = [
                'invoice_file' => null,
                'origin_cert_file' => null,
                'health_cert_file' => null,
            ];

            foreach ($fileFields as $field) {
                if (!isset($files[$field])) {
                    continue;
                }

                if (
                    (int)($files[$field]['error'] ?? UPLOAD_ERR_NO_FILE)
                    === UPLOAD_ERR_NO_FILE
                ) {
                    continue;
                }

                $result = $this->uploader->upload($files[$field]);

                if (!$result['success']) {
                    throw new RuntimeException(
                        $result['error'] ?? 'تعذر رفع أحد الملفات.'
                    );
                }

                $uploadedFiles[$field] = $result['path'];

                if ($result['path'] !== null) {
                    $uploadedPaths[] = $result['path'];
                }
            }

            $this->pdo->beginTransaction();

            $client = null;

            if ($data['existing_client_id'] !== '') {
                $client = $this->importerBroker->findById(
                    (int)$data['existing_client_id']
                );

                if ($client === null) {
                    throw new RuntimeException(
                        'الشركة المحددة غير موجودة.'
                    );
                }

                $this->importerBroker->updateContact(
                    (int)$client['client_id'],
                    $data['phone'],
                    $data['email']
                );
            } else {
                $client = $this->importerBroker->findByCompanyName(
                    $data['company_name']
                );

                if ($client !== null) {
                    $this->importerBroker->updateContact(
                        (int)$client['client_id'],
                        $data['phone'],
                        $data['email']
                    );
                } else {
                    $licenseNumber = $this->licenseNumberGenerator
                        ->generate($this->pdo);

                    $clientId = $this->importerBroker->create(
                        $data['company_name'],
                        $licenseNumber,
                        $data['phone'],
                        $data['email']
                    );

                    $client = $this->importerBroker->findById($clientId);

                    if ($client === null) {
                        throw new RuntimeException(
                            'تعذر استرجاع بيانات الشركة بعد إنشائها.'
                        );
                    }
                }
            }

            $declarationId = $this->declarationNumberGenerator
                ->generate($this->pdo);

            $declarationData = [
                'declaration_id' => $declarationId,
                'client_id' => (int)$client['client_id'],
                'origin_country' => $data['origin_country'],
                'shipment_type' => $data['shipment_type'],
                'total_weight' => (float)$data['total_weight'],
                'packages_count' => (int)$data['packages_count'],
                'declared_value' => (float)$data['declared_value'],
                'port_of_entry' => $data['port_of_entry'],
                'container_number' => $data['container_number'],
                'driver_name' => $data['driver_name'],
                'invoice_file' => $uploadedFiles['invoice_file'],
                'origin_cert_file' => $uploadedFiles['origin_cert_file'],
                'health_cert_file' => $uploadedFiles['health_cert_file'],
            ];

            $savedDeclarationId = $this->declarationService
                ->createDeclaration($declarationData, $items);

            $this->pdo->commit();

            return [
                'success' => true,
                'declaration_id' => $savedDeclarationId,
                'license_number' => $client['license_number'],
                'items_count' => count($items),
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            foreach ($uploadedPaths as $path) {
                $this->uploader->delete($path);
            }

            error_log(
                'Declaration processing failed: ' . $e->getMessage()
            );

            return [
                'success' => false,
                'errors' => [
                    'تعذر حفظ البيان. تحقق من البيانات وحاول مرة أخرى.',
                ],
            ];
        }
    }
}