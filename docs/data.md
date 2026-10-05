# Factories وSeeders وبيانات الاختبار

## DatabaseSeeder

`database/seeders/DatabaseSeeder.php` يستدعي:

```php
$this->call(HrSeeder::class);
```

لتشغيل Seeder:

```bash
php artisan db:seed
```

لإضافة أو تحديث الصلاحيات ودور مدير القسم في قاعدة موجودة دون إضافة بيانات تجريبية جديدة:

```bash
php artisan db:seed --class=OrganizationPermissionSeeder
```

## HrSeeder

`HrSeeder` ينشئ بيانات مترابطة:

1. مستخدم Admin بالبريد `admin@example.com` وكلمة مرور `password`.
2. دور `hr_admin` وربطه بصلاحيات النظام.
3. ثلاثة أقسام وخمس وظائف.
4. ملف موظف لمدير HR إذا لم يكن موجودًا.
5. عشرة موظفين تجريبيين.

يستدعي `HrSeeder` أيضًا `OrganizationPermissionSeeder`، الذي ينشئ صلاحيات الموارد ودور `department_manager` ويمنحه صلاحيات القراءة المحدودة بالقسم. لا يمنح هذا الدور صلاحيات اعتماد أو إدارة.

المستخدم والصلاحيات والأدوار الثابتة تستخدم `firstOrCreate`، لكن الأقسام والوظائف والموظفين تُنشأ عبر factories؛ لذلك كل تشغيل متكرر لـ`db:seed` يضيف بيانات تجريبية جديدة.

## بيانات الدخول المحلية

```text
Email: admin@example.com
Password: password
```

كلمة المرور موجودة لأغراض التطوير فقط.

## Factories الحالية

| Factory             | Model      | الوظيفة                            |
| ------------------- | ---------- | ---------------------------------- |
| `UserFactory`       | User       | حسابات مستخدمين                    |
| `RoleFactory`       | Role       | أدوار عشوائية                      |
| `PermissionFactory` | Permission | صلاحيات عشوائية                    |
| `DepartmentFactory` | Department | أقسام                              |
| `PositionFactory`   | Position   | وظائف                              |
| `EmployeeFactory`   | Employee   | موظفون مع User/Department/Position |

## EmployeeFactory

ينشئ الموظف مع:

- User جديد.
- Department جديد افتراضيًا.
- Position جديد افتراضيًا.
- رقم وظيفي فريد.
- راتب أساسي موجب.
- تاريخي توظيف وميلاد.
- وردية `day` أو `night`.

عند استخدام Seeder، يتم تمرير قسم ووظيفة من السجلات الثلاثة والخمسة حتى تكون البيانات أكثر ترابطًا.

## الاستخدام في الاختبارات

```php
$user = User::factory()->create();
$employee = Employee::factory()->create([
    'user_id' => $user->id,
]);
```

لإنشاء عدة موظفين:

```php
$employees = Employee::factory()->count(10)->create();
```

## HasFactory

النماذج التي تملك Factory تستخدم `HasFactory`:

- User
- Role
- Permission
- Department
- Position
- Employee

## ملاحظات التكرار

- `EmployeeFactory` ينشئ علاقات تلقائية، وقد ينتج أقسامًا ووظائف إضافية عند استخدامه منفردًا.
- استخدم `recycle()` أو مرر IDs محددة في الاختبارات عندما تحتاج مشاركة نفس العلاقات.
- لا تستخدم `migrate:fresh --seed` على قاعدة بيانات تحتوي بيانات حقيقية؛ الأمر يحذف كل الجداول.
