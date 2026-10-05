# HR System Documentation

هذا المجلد هو بوابة التوثيق الرسمية للمشروع. المشروع عبارة عن HR API لشركة واحدة، مبني على Laravel 13 وPHP 8.3 وMySQL وSanctum.

## ابدأ من هنا

1. [دليل التشغيل](setup.md)
2. [المعمارية وبنية الكود](architecture.md)
3. [توثيق API والمصادقة](api.md)
4. [قواعد البيانات والـ migrations](database-migrations.md)
5. [Factories وSeeders وبيانات الاختبار](data.md)
6. [قواعد الأمان والتطوير](security-and-development.md)
7. [مرجع API المختصر](api-reference.md)
8. [حالة المشروع والفجوات](project-status.md)
9. [مجموعة Postman](postman/HR-Management.postman_collection.json)

## التقنية الحالية

| المجال             | التقنية                                    |
| ------------------ | ------------------------------------------ |
| Backend            | Laravel 13                                 |
| PHP                | 8.3 أو أحدث ضمن السلسلة المدعومة           |
| Database           | MySQL                                      |
| API Authentication | Laravel Sanctum Personal Access Tokens     |
| ORM                | Eloquent                                   |
| Validation         | Form Requests                              |
| Data access        | Repository Pattern                         |
| JSON responses     | API Resources                              |
| Testing            | Pest 4 / PHPUnit 12                        |
| Formatting         | Laravel Pint                               |
| API locale         | العربية افتراضيًا مع دعم `Accept-Language` |

## النطاق الوظيفي

- المستخدمون وتسجيل الدخول.
- الأدوار والصلاحيات.
- الأقسام والوظائف.
- الموظفون والورديات النهارية والليلية.
- الحضور والغياب، بما في ذلك الوردية التي تعبر منتصف الليل.
- المستندات.
- الإجازات وأرصدة الإجازات.
- العطل الرسمية.
- الرواتب الشهرية.
- إعدادات الشركة.
- إشعارات Laravel.

## مصدر الحقيقة

- Routes: `routes/api.php`.
- Validation: `app/Http/Requests`.
- JSON responses: `app/Http/Resources`.
- Data access: `app/Repositories`.
- Database schema: `database/migrations`.
- Seed data: `database/seeders`.

## ملاحظات مهمة

- كل مسارات HR API محمية بـ `auth:sanctum` ما عدا تسجيل الدخول.
- ملفات migrations منظمة في مجلدات فرعية ومسجلة في `AppServiceProvider`.
- إذا كانت migration قد نُفذت في بيئة مشتركة، لا تعدلها؛ أنشئ migration جديدة.
- حساب Seeder التجريبي هو `admin@example.com` وكلمة المرور `password` للاستخدام المحلي فقط.
- يجب تغيير بيانات الدخول التجريبية قبل أي بيئة حقيقية.
- مجموعة Postman وخطوات تسجيل الدخول موثقة في [دليل API](api.md).
