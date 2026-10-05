# توثيق API والمصادقة

## عنوان API المحلي

```text
http://127.0.0.1:8000/api
```

أضف دائمًا:

```http
Accept: application/json
Content-Type: application/json
Accept-Language: ar
```

في رفع الملفات استخدم `multipart/form-data` ودع Postman يضبط `Content-Type` وboundary تلقائيًا؛ لا تضع `application/json` لهذا الطلب.

استورد [مجموعة Postman](postman/HR-Management.postman_collection.json)، واضبط `baseUrl` على `http://127.0.0.1:8000` ثم نفّذ طلب Login؛ المجموعة تحفظ الرمز تلقائيًا. طلبات القوائم والإنشاء تحفظ أول ID تلقائيًا. لا يوجد users endpoint؛ اضبط `userId` يدويًا على مستخدم موجود غير مرتبط بموظف قبل اختبار إنشاء موظف. استخدم `Accept-Language: en` لرسائل إنجليزية.

## المصادقة باستخدام Sanctum

### تسجيل الدخول

```http
POST /api/auth/login
```

Body:

```json
{
    "email": "admin@example.com",
    "password": "password",
    "device_name": "postman"
}
```

الاستجابة الناجحة:

```json
{
    "message": "تم تسجيل الدخول بنجاح.",
    "token": "1|plain-text-token",
    "token_type": "Bearer",
    "user": {}
}
```

احفظ `token` محليًا ولا تضعه في Git أو logs.

### استعمال المسارات المحمية

```http
Authorization: Bearer 1|plain-text-token
Accept: application/json
```

### المستخدم الحالي

```http
GET /api/auth/me
```

### تسجيل الخروج

```http
POST /api/auth/logout
```

يحذف token الحالي فقط. لإلغاء كل الجلسات يجب حذف tokens الخاصة بالمستخدم من منطق مخصص وآمن.

## المسارات العامة

| Method | URI           | الغرض               |
| ------ | ------------- | ------------------- |
| POST   | `/auth/login` | إصدار Sanctum token |

## مسارات المصادقة المحمية

| Method | URI            | الغرض              |
| ------ | -------------- | ------------------ |
| GET    | `/auth/me`     | المستخدم الحالي    |
| POST   | `/auth/logout` | إلغاء token الحالي |

## المسارات حسب المورد

| المورد                                                 | العمليات                                        | الصلاحيات الأساسية                                                 |
| ------------------------------------------------------ | ----------------------------------------------- | ------------------------------------------------------------------ |
| employees, departments, positions, documents, holidays | GET/POST/GET id/PATCH/DELETE                    | `resource.view` للقراءة و`resource.manage` للكتابة                 |
| attendances                                            | GET/POST/GET id/PATCH؛ لا يوجد DELETE           | `attendance.view`, `attendance.manage`                             |
| leaves                                                 | GET/POST/GET id/PATCH/DELETE، وقرار منفصل       | `leaves.view`, `leaves.manage`, `leaves.approve`                   |
| leave-balances                                         | GET/POST/GET id/PATCH/DELETE                    | `leave-balances.view`, `leave-balances.manage`                     |
| salaries                                               | GET/POST/GET id/PATCH/DELETE، قرار ودفع منفصلان | `payroll.view`, `payroll.manage`, `payroll.approve`, `payroll.pay` |
| roles                                                  | CRUD                                            | `roles.manage`                                                     |
| permissions                                            | CRUD                                            | `permissions.manage`                                               |
| organization                                           | GET/PATCH فقط                                   | `organization.manage`                                              |

الإجازات والرواتب لا يمكن تعديلها بعد المراجعة، ولا حذف الراتب إلا وهو مسودة. حدّ `per_page` من 1 إلى 100، والافتراضي 15.

## المؤسسة

بيانات المؤسسة سجل singleton واحد، وليست CRUD لمجموعة إعدادات:

```text
GET   /api/organization
PATCH /api/organization
```

المساران يتطلبان Sanctum وصلاحية `organization.manage` من دور فعال. تحديث المؤسسة محدود إلى 30 طلبًا في الدقيقة. عند أول قراءة، ينشئ الخادم إعدادًا افتراضيًا واحدًا إن لم يوجد.

مثال تحديث:

```json
{
    "company_name": "Example Company",
    "timezone": "Asia/Riyadh",
    "work_start_time": "08:00:00",
    "work_end_time": "17:00:00",
    "weekend_days": ["Friday", "Saturday"],
    "annual_leave_days": 21
}
```

## الصلاحيات حسب المورد

- القراءة تتطلب `resource.view`، وصلاحية `resource.manage` تتضمن القراءة.
- الإنشاء والتعديل والحذف تتطلب `resource.manage`.
- إدارة الأدوار والصلاحيات تتطلب `roles.manage` و`permissions.manage`.
- الرواتب تستخدم `payroll.view`, `payroll.manage`, `payroll.approve`, و`payroll.pay`.
- الإجازات تستخدم `leaves.view`, `leaves.manage`, و`leaves.approve`.
- صلاحية الإدارة لا تمنح تلقائيًا اعتماد الإجازة أو الراتب.
- `hr_admin` يرى جميع السجلات. `department_manager` يرى سجلات قسمه ضمن الموارد التي يملك صلاحية قراءتها، والموظف يرى سجلاته.
- يجب ربط مدير القسم بملف موظف لتحديد قسمه. السجل خارج النطاق يرجع 404 لإخفاء وجوده.

## اعتماد الإجازة

```http
POST /api/leaves/{leave}/decision
Authorization: Bearer TOKEN
```

```json
{ "decision": "approved" }
```

للرفض، أرسل `decision: rejected` مع `rejection_reason`. الخادم يحسب أيام العمل، يمنع تداخل الإجازات المعتمدة، يتحقق من الرصيد السنوي، ويسجل الموظف المرتبط بحساب المعتمد. لا يقبل إنشاء/تحديث الإجازة حقول `status`, `approved_by`, أو `total_days` من العميل.

## دورة الرواتب

- إنشاء/تعديل المسودة لا يقبل `net_amount`, `status`, أو `paid_at` من العميل.
- `net_amount = basic_amount + bonus + overtime_amount - deductions` ويحسب على الخادم.
- الاعتماد: `POST /api/salaries/{salary}/decision` مع `decision: approved` أو `rejected`.
- الدفع: `POST /api/salaries/{salary}/pay`، ولا يقبل إلا راتبًا معتمدًا غير مدفوع.
- تحفظ حقول `reviewed_by_user_id`, `reviewed_at`, و`paid_by_user_id` للتدقيق.

## مستندات الموظفين

`POST /api/documents` و`PATCH /api/documents/{id}` يقبلان ملفًا في الحقل `file` فقط، PDF أو JPG أو PNG حتى 10 MB. يخزن على disk خاص، ولا يعيد API مسار التخزين. التنزيل:

```http
GET /api/documents/{document}/download
```

يتطلب `documents.view`. حذف المستند soft delete ويبقي الملف للأرشفة.

## Pagination

كل `index` يقبل:

```text
GET /api/employees?per_page=25
```

القيمة مقيدة بين 1 و100، والافتراضي 15.

## HTTP status codes

| Code | المعنى                                           |
| ---- | ------------------------------------------------ |
| 200  | نجاح القراءة أو التحديث                          |
| 201  | إنشاء سجل بنجاح                                  |
| 204  | حذف أو logout بنجاح بدون body                    |
| 401  | token مفقود أو غير صحيح أو بيانات دخول غير صحيحة |
| 403  | الحساب غير نشط أو غير مصرح                       |
| 404  | المورد غير موجود أو خارج نطاق الصلاحية           |
| 409  | تعارض بيانات متزامن مع قيد فريد                  |
| 422  | فشل التحقق؛ تفاصيل الحقول في `errors`            |
| 429  | تجاوز حد الطلبات                                 |
| 500  | خطأ غير معالج في الخادم                          |

## التحقق ورسائل الخطأ

أرسل `Accept-Language: ar` للرسائل العربية، أو `en` للإنجليزية. مثال خطأ تحقق عربي:

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["حقل البريد الإلكتروني مطلوب."]
    }
}
```

القواعد الخاصة بالمورد موجودة في `app/Http/Requests/<Resource>`.

## أمثلة إنشاء موظف

```http
POST /api/employees
Authorization: Bearer TOKEN
Content-Type: application/json
```

```json
{
    "user_id": 2,
    "department_id": 1,
    "position_id": 2,
    "employee_code": "EMP-0001",
    "name": "Ahmed Ali",
    "email": "ahmed@example.com",
    "basic_salary": 8500,
    "hire_date": "2026-01-01",
    "birth_date": "1995-05-10",
    "gender": "male",
    "status": "active",
    "work_shift": "night"
}
```

## مثال حضور وردية ليلية

```json
{
    "employee_id": 7,
    "attendance_date": "2026-09-27",
    "shift": "night",
    "check_in_at": "2026-09-27 23:00:00",
    "check_out_at": "2026-09-28 07:00:00",
    "status": "present",
    "late_minutes": 0,
    "overtime_minutes": 0,
    "source": "web"
}
```

`attendance_date` هو تاريخ بداية الوردية، حتى إذا كان `check_out_at` في اليوم التالي.

## مثال إجازة

```json
{
    "employee_id": 7,
    "leave_type": "earned",
    "start_date": "2026-10-01",
    "end_date": "2026-10-03",
    "reason": "Personal leave"
}
```

## مثال راتب

```json
{
    "employee_id": 7,
    "year": 2026,
    "month": 9,
    "working_days": 22,
    "absent_days": 0,
    "holiday_days": 2,
    "basic_amount": 8500,
    "deductions": 250,
    "bonus": 500,
    "overtime_amount": 300
}
```

يحسِب الخادم `net_amount` ويبدأ الراتب بحالة `pending`؛ لا ترسل `net_amount`, `status`, `paid_at`, أو حقول التدقيق من العميل.

لجدول مختصر لكل الموارد والحقول، راجع [مرجع API المختصر](api-reference.md). وللفجوات المطلوبة قبل الإنتاج، راجع [حالة المشروع](project-status.md).
