# خريطة المنطق العملي للمشروع

## الأساس

هذا المشروع لا يبنى كـ CRUD عادي. هو نظام HR قائم على workflows حقيقية، وكل وحدة لها:

- حالة تشغيلية
- قواعد أعمال
- صلاحية وصول
- حساب على الخادم
- تدقيق
- منع التلاعب من العميل

القاعدة الذهبية هي:

> العميل يرسل البيانات الأساسية فقط، أما القرار النهائي والحسابات الحساسة فيتقررها الخادم.

---

## 1) ترتيب التنفيذ المنطقي الصحيح

### المرحلة 1: Payroll

Payroll هو أول موديول لأنّه الأكثر حساسية وأوسع أثرًا في النظام.

#### المنطق

- إنشاء مسودة راتب
- حساب صافي الراتب على الخادم
- تعيين الحالة كـ pending
- مراجعة الراتب: approved أو rejected
- الدفع فقط بعد الاعتماد
- حفظ من راجع ومن دفع
- منع تعديل أو حذف راتب تم مراجعته أو دفعه

#### القواعد الأساسية

- لا يُسمح للعميل بإرسال net_amount
- لا يُسمح بإرسال status
- لا يُسمح بإرسال paid_at
- لا يُسمح بالتلاعب في reviewed_by_user_id أو paid_by_user_id

#### مرجع التنفيذ

- app/Services/Salary/SalaryWorkflowService.php
- app/Http/Controllers/Api/SalaryController.php
- routes/api.php
- app/Providers/AppServiceProvider.php

---

### المرحلة 2: Leave

Leave هو ثاني موديول مهم لأنه يربط الموظف مع الرصيد والقرار والتاريخ.

#### المنطق

- إنشاء طلب إجازة
- حساب أيام العمل الفعلية
- استبعاد عطلات الأسبوع والعطل الرسمية
- فحص التداخل مع إجازات معتمدة
- فحص رصيد الإجازة السنوي
- اعتماد أو رفض الطلب
- عدم السماح بتعديل أو حذف الطلب بعد المراجعة

#### القواعد الأساسية

- لا يُسمح للعميل بإرسال total_days
- لا يُسمح للعميل بتحديد status
- لا يُسمح باعتماد إجازة تتداخل مع إجازة معتمدة أخرى
- لا يُسمح بتجاوز رصيد الإجازة

#### مرجع التنفيذ

- app/Services/Leave/LeaveWorkflowService.php
- app/Http/Controllers/Api/LeaveController.php

---

### المرحلة 3: LeaveBalance

LeaveBalance يحدد الرصيد السنوي لكل موظف حسب نوع الإجازة.

#### المنطق

- employee_id
- year
- leave_type
- allocated_days
- used_days

#### القاعدة المهمة

- لا يمكن اعتماد إجازة إذا تجاوزت days المستخدمة الرصيد المخصص

#### مرجع التنفيذ

- app/Models/LeaveBalance.php
- app/Http/Requests/LeaveBalance/StoreLeaveBalanceRequest.php

---

### المرحلة 4: Attendance

Attendance هو نظام التزام الموظف بالوقت والوردية.

#### المنطق

- تطابق shift مع work_shift للموظع
  -Must match attendance_date with check_in_at date
- check_out_at يجب أن يكون بعد check_in_at
- الحضور لا يكون صحيحًا إذا كانت الوردية غير متوافقة مع السجل

#### القواعد الأساسية

- الوردية لا تتعارض مع الوردية الأساسية للموظف
- وقت الدخول يجب أن يكون في نفس تاريخ اليوم المسجل
- وقت الخروج يجب أن يكون بعد وقت الدخول

#### مرجع التنفيذ

- app/Http/Requests/Attendance/StoreAttendanceRequest.php
- app/Http/Requests/Attendance/UpdateAttendanceRequest.php
- app/Models/Attendance.php

---

### المرحلة 5: Documents

المستندات لا تُدار كملفات عادية بل كـ مستندات HR محمية.

#### المنطق

- رفع الملفات إلى مساحة خاصة
- عدم إعادة مسار الملف إلى العميل
- تنزيل محمي
- حذف soft delete للأرشفة

#### القاعدة الأساسية

- لا يُسمح للكود أو العميل بتخزين مسار ملف مباشر في قاعدة البيانات

#### مرجع التنفيذ

- app/Services/Document/DocumentFileService.php
- app/Http/Controllers/Api/DocumentController.php
- app/Models/Document.php

---

### المرحلة 6: Policies / Authorization

بعد استقرار الوحدات الأساسية، يأتي ملف الأمان التنظيمي.

#### المنطق

- فصل الصلاحيات عن منطق العمل
- استخدام Policies بدل Gates في المرحلة المستقبلية
- فصل allowed actions عن data access logic

#### لماذا مهم؟

لأن HR يحتاج صلاحية دقيقة لكل مورد:

- view
- manage
- approve
- pay

#### مرجع التنفيذ

- app/Providers/AppServiceProvider.php
- routes/api.php

---

### المرحلة 7: Reports

بعد إكمال سير العمل، تأتي التقارير.

#### التقارير المطلوبة

- الرواتب الشهرية
- الحضور والغياب
- الإجازات
- الرصيد السنوي
- حالات الموافقة والدفع

---

### المرحلة 8: SaaS / Multi-company

هذه المرحلة لا تبدأ في البداية، لأنها تغير هيكل المشروع بالكامل.

#### المنطق

- إضافة company_id
- عزل البيانات حسب الشركة
- صلاحيات حسب المؤسسة
- قابلية التوسع إلى بيئات متعددة

---

## 2) التصميم الصحيح للمشروع

### طبقات التصميم

1. Request Layer
    - التحقق من البيانات الواردة
    - مثل StoreSalaryRequest و StoreLeaveRequest

2. Service Layer
    - منطق الأعمال الحقيقي
    - مثل SalaryWorkflowService و LeaveWorkflowService

3. Repository Layer
    - الوصول إلى البيانات
    - تشغيل الاستعلامات دون منطق الأعمال

4. Controller Layer
    - رقيق جدًا
    - يمرر الطلب إلى الخدمة

5. Route Layer
    - يحدد المسار
    - يربطه بالصلاحية المناسبة

6. Authorization Layer
    - يحدد من يملك action معين

7. Database Layer
    - العلاقات، العلاقات الخارجية، الفهارس، القيم الافتراضية

---

## 3) القاعدة النهائية

المشروع يُبنى على هذه الفكرة:

- العميل يدخل البيانات
- الخادم يقرر القيم الحساسة
- الخادم يطبق القوانين
- الخادم يمنع التلاعب
- الخادم يسجل التدقيق

وبالتالي، النظام الصحيح في HR ليس مجرد قائمة من الجداول، بل هو سلسلة من workflows منسجمة، كل واحد منها محمي بالكامل.

---

## 4) الترتيب النهائي للمشروع

Payroll → Leave → LeaveBalance → Attendance → Documents → Policies → Reports → SaaS

هذا هو الترتيب المنطقي الحقيقي للمشروع، بناءً على أولوية الأعمال، وأمان البيانات، وأداء النظام.

---

## 5) ما الذي ما يزال ناقصًا فعليًا

### الأولوية 1: Policies و Authorization

- تحويل Gates الحالية إلى Policies Laravel
- توحيد naming scheme لكل resource
- تحديد `view/manage/approve/pay` بشكل موحد
- إزالة أي تداخل بين الصلاحية والمنطق

### الأولوية 2: Reports and Dashboard

- تقارير الرواتب الشهرية
- تقارير الحضور والغياب
- تقارير الإجازات والأرصدة
- لوحات تحكم المديرين
- تصدير CSV / PDF أساسي

### الأولوية 3: Audit history و Change log

- تسجيل كل قرار مراجعة
- حفظ من قام بالموافقة أو الرفض أو الدفع
- تتبع تغييرات البيانات الحساسة
- تسهيل التحقيق والعمليات الإدارية

### الأولوية 4: SaaS / Multi-company

- إضافة `company_id` إلى الموارد الأساسية
- عزل بيانات الشركة
- إعداد scopes حسب المؤسسة
- سياسات الوصول حسب tenant

### الأولوية 5: Documentation modules

- Payroll module doc كامل
- Leave module doc كامل
- Attendance module doc كامل
- LeaveBalance module doc كامل
- Documents module doc كامل
- مراجعة إقتران الوثيقة مع التطبيق الفعلي

---

## 6) خطة التنفيذ الفعلية الآن

### المرحلة الحالية (العمل الفوري)

1. إتمام Policies بدل Gates
2. التأكد أن كل route يملك صلاحية دقيقة
3. توحيد naming and permission semantics
4. بناء Reports الأساسية
5. التأكد من Audit trail لكل workflow

### المرحلة التالية

1. إضافة دعم company_id
2. تجميع البيانات حسب الشركة
3. بناء dashboard HR الأساسي
4. توحيد المعالجة للأدوار الجديدة

### المرحلة النهائية

1. تنعيم أداء الاستعلامات
2. بناء APIs للتقارير
3. إعداد طبقة SaaS كاملة
4. توثيق final architecture

---

## 7) الخلاصة التنفيذية

المشروع في هذه اللحظة مستقر من ناحية workflow الأساسي، لكنه ما يزال يحتاج:

- توحيد authorization
- توسيع تقارير HR
- بناء audit history
- تحضير الشركة الواحدة للتوسع إلى SaaS

وهذا هو ما يفتح الطريق إلى مرحلة الإنتاج الجدية مع الاحتفاظ بنفس منطق HR الصحيح.
