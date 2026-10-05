# المعمارية وبنية المشروع

## المبدأ العام

المشروع API مبني على فصل المسؤوليات. كل طبقة لها وظيفة محددة:

```text
HTTP Request
    -> Route
    -> Middleware
    -> Controller
    -> FormRequest
    -> Repository Interface
    -> Repository خاص
    -> BaseRepository
    -> Eloquent Model
    -> API Resource
    -> JSON Response
```

## بنية المجلدات المهمة

```text
app/
├── Http/
│   ├── Controllers/Api/
│   ├── Requests/
│   │   ├── Auth/
│   │   ├── Employee/
│   │   ├── Department/
│   │   ├── Position/
│   │   ├── Document/
│   │   ├── Holiday/
│   │   ├── Attendance/
│   │   ├── Leave/
│   │   ├── LeaveBalance/
│   │   ├── Salary/
│   │   ├── Organization/
│   │   ├── Role/
│   │   └── Permission/
│   └── Resources/
├── Models/
├── Repositories/
│   ├── Contracts/RepositoryInterface.php
│   ├── Shared/BaseRepository.php
│   └── <Resource>/
└── Providers/AppServiceProvider.php

database/
├── factories/
├── migrations/
│   ├── 01-foundation/
│   ├── 02-authorization/
│   ├── 03-company-structure/
│   ├── 04-employee-records/
│   ├── 05-leave-management/
│   └── 06-payroll-notifications/
└── seeders/
```

## Routes

`routes/api.php` يحمّل ملفات المجال من `routes/api/`: `auth`, `organization`, `workforce`, و`administration`. مسار login عام ومحدود المعدل، وبقية المسارات تستخدم `auth:sanctum` وGates المورد المناسبة.

## Controllers

المتحكم مسؤول عن:

- استقبال FormRequest.
- طلب البيانات الموثقة عبر `$request->validated()`.
- استدعاء Repository Interface.
- تحويل النتيجة إلى Resource.
- تحديد status code عند الحاجة.

المتحكم لا يجب أن يحتوي على:

- استعلامات Eloquent مباشرة.
- قواعد validation.
- منطق حساب الراتب.
- منطق اعتماد الإجازة.
- منطق إدارة الملفات.

## Form Requests

كل مورد يملك Request للإنشاء وآخر للتحديث. مكانها:

```text
app/Http/Requests/<Resource>/Store<Resource>Request.php
app/Http/Requests/<Resource>/Update<Resource>Request.php
```

الطلبات ترث من `SanitizingFormRequest` لتنظيف النصوص قبل التحقق، مع الحفاظ على قيم كلمات المرور والتوكنات. قواعد التحقق والرسائل العامة في `lang/ar/validation.php`، وتضاف رسائل محلية عند الحاجة لمعنى المورد.

يجب أن تحتوي على:

- `authorize()` للتفويض الخاص بالطلب عند الحاجة؛ التفويض العام يطبّق عبر route middleware وGates.
- `rules()` لقواعد التحقق.
- `messages()` للرسائل المخصصة عند الحاجة.

تقيّد Gates العمليات بالـ permissions، وتستخدم `DataVisibility` لتقييد بيانات الموظفين الحساسة حسب الدور والقسم. API المؤسسة محمية بـ `manage-organization` وتتطلب مستخدمًا نشطًا لديه `organization.manage`.

## Repositories

### العقد العام

`Repositories/Contracts/RepositoryInterface.php` يعرف العمليات المشتركة:

- `paginate`
- `find`
- `create`
- `update`
- `delete`

### التنفيذ العام

`Repositories/Shared/BaseRepository.php` ينفذ العمليات المشتركة باستخدام Model يمرر في constructor.

### التنفيذ الخاص

كل مورد يملك:

```text
<Resource>RepositoryInterface
<Resource>Repository
```

الـ interface الخاص يرث العقد العام، والـ Repository الخاص يرث `BaseRepository`. يتم وضع eager loading والعمليات الخاصة بالمورد داخل Repository الخاص.

### ربط Laravel Container

يتم الربط في `AppServiceProvider::register()`:

```php
$this->app->bind(
    EmployeeRepositoryInterface::class,
    EmployeeRepository::class,
);
```

بهذا يعتمد Controller على العقد وليس على التنفيذ.

## Models

النماذج مسؤولة عن:

- تمثيل الجدول.
- العلاقات.
- `fillable` أو Attributes Fillable.
- casts للتواريخ والمال وJSON وBoolean.
- SoftDeletes عند وجود `deleted_at`.
- HasFactory عند وجود Factory.

## API Resources

تستخدم Resources لتحديد شكل JSON العام بدل إعادة Model كامل عشوائيًا. عند إضافة حقل جديد، راجع Resource الخاص بالمورد.

## Eager Loading

المستودعات تحمل العلاقات المتكررة مسبقًا لتقليل N+1، مثل:

```php
EmployeeRepository -> department, position
LeaveRepository -> employee, approver
AttendanceRepository -> employee
SalaryRepository -> employee
```

## قواعد التسمية

- Models: مفرد TitleCase مثل `Employee`.
- Controllers: `EmployeeController`.
- Requests: `StoreEmployeeRequest` و`UpdateEmployeeRequest`.
- Resources: `EmployeeResource`.
- Repository: `EmployeeRepository`.
- Interface: `EmployeeRepositoryInterface`.
- Database tables: جمع snake_case.
- Route parameters: مطابقة اسم المورد، مثل `{leave}` للإجازات.

## إضافة مورد جديد

1. أنشئ Model وMigration وFactory عند الحاجة.
2. أنشئ Repository Interface وRepository داخل مجلد المورد.
3. سجّل binding في `AppServiceProvider`.
4. أنشئ Store/Update Form Requests.
5. أنشئ Resource.
6. أنشئ Controller API.
7. أضف Route داخل مجموعة Sanctum.
8. أضف Seeder أو Factory عند الحاجة.
9. أضف Feature tests.
10. شغّل syntax وPint وtests وroute:list.
