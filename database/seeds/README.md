# Database Seeds

هذا المجلد مخصص لبيانات البداية (seed data) لقاعدة البيانات.

## الحالة الحالية
فارغ. عند إعداد نسخة جديدة من المشروع، تُنشأ البيانات يدوياً.

## خطة مستقبلية
- `demo_users.sql` — مستخدمون تجريبيون (admin, inspector, accountant)
- `demo_declarations.sql` — بيانات جمركية للعرض
- `demo_permits.sql` — تصاريح إفراج للعرض

## الاستخدام
```bash
mysql -u root cms_db < database/seeds/demo_users.sql