<?php
declare(strict_types=1);

class DeclarationService
{
    private PDO $pdo;
    private Declaration $declarationModel;

    public function __construct(
        PDO $pdo,
        Declaration $declarationModel
    ) {
        $this->pdo = $pdo;
        $this->declarationModel = $declarationModel;
    }

    public function createDeclaration(
        array $data,
        array $items
    ): string {
        $ownsTransaction = !$this->pdo->inTransaction();

        if ($ownsTransaction) {
            $this->pdo->beginTransaction();
        }

        try {
            $declarationId = $this->declarationModel->create($data);

            $this->declarationModel->addItems(
                $declarationId,
                $items
            );

            if ($ownsTransaction) {
                $this->pdo->commit();
            }

            return $declarationId;
        } catch (Throwable $e) {
            if ($ownsTransaction && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}