# QueueNow Clinic MVP

## Software Requirements Specification (SRS)

**Project Name:** QueueNow
**Document:** Software Requirements Specification
**Version:** 1.0
**Project Type:** Web Application
**Scope:** Minimum Viable Product (MVP)
**MVP Deadline:** 1 Week

---

# 1. Introduction

## 1.1 Purpose

يهدف **QueueNow** إلى تنظيم وإدارة الطوابير وأدوار الانتظار في العيادات والمواقع التي تعتمد على نظام الدور.

يركز النظام في نسخته الأولى على حل مشكلة الانتظار التقليدي، حيث يحصل المراجع على Ticket برقم دوره، ثم يستطيع متابعة حالة دوره ومعرفة عدد الأشخاص الموجودين قبله دون الحاجة إلى الوقوف بشكل مستمر في الطابور.

في المقابل، يحصل الموظف على Dashboard تمكنه من إدارة الطابور وتحديث حالة الـTickets أثناء تقديم الخدمة.

---

## 1.2 Product Vision

يوفر QueueNow تجربة بسيطة للمراجع والموظف:

```text
Customer / Visitor
        ↓
     Take Ticket
        ↓
   Receive Number
        ↓
   Track Queue
        ↓
      Called
        ↓
     Serving
        ↓
       Done
```

ويستطيع الموظف إدارة الطابور من خلال:

```text
Queue Dashboard
      ↓
    Next
      ↓
    Start
      ↓
     Done
```

مع إمكانية استخدام:

```text
Skip
Cancel
```

عند الحاجة.

---

# 2. Scope

## 2.1 In Scope

يشمل MVP الوظائف التالية:

* إنشاء Ticket.
* إعطاء Ticket Number للمراجع.
* عرض حالة الـTicket.
* عرض عدد الأشخاص الموجودين قبله.
* عرض قائمة الانتظار للموظف.
* الانتقال إلى Ticket التالية.
* بدء خدمة Ticket.
* إنهاء Ticket.
* تخطي Ticket.
* إلغاء Ticket.
* حفظ جميع Tickets في قاعدة البيانات.
* الاحتفاظ بسجل Tickets وعدم حذفها.
* عدم إعادة استخدام رقم Ticket ضمن نفس اليوم ونفس الخدمة.
* حساب وعرض Estimated Waiting Time.
* ربط Frontend مع Backend وقاعدة البيانات في نظام واحد يعمل بشكل كامل.
* إدارة المشروع بالكامل عبر GitHub.

---

## 2.2 Out of Scope

لن تشمل نسخة MVP الحالية:

* Medical Records.
* Patient Medical History.
* Prescriptions.
* Billing.
* Insurance.
* Pharmacy Integration.
* Laboratory Integration.
* Online Payments.
* Mobile Application.
* SMS / WhatsApp Integration.
* Complex Admin Panel.
* Multi-branch Management.
* AI Diagnosis.
* AI Medical Recommendations.
* أي وظائف لا ترتبط مباشرة بإدارة الطوابير والانتظار.

يمكن إضافة هذه الوظائف في إصدارات مستقبلية.

---

# 3. Stakeholders

## 3.1 Customer / Visitor

الشخص الذي يزور العيادة ويحصل على Ticket وينتظر دوره.

## 3.2 Employee

الموظف المسؤول عن إدارة الطابور وتقديم الخدمة للمراجعين.

## 3.3 Manager / Admin

دور محتمل في المستقبل، وليس جزءًا أساسيًا من MVP الحالي.

---

# 4. User Roles

الإصدار الحالي يعتمد على مستخدمين رئيسيين:

### Customer / Visitor

* أخذ Ticket.
* مشاهدة رقم الدور.
* مشاهدة حالة Ticket.
* مشاهدة عدد الأشخاص قبله.
* مشاهدة Estimated Waiting Time.

### Employee

* مشاهدة Queue Dashboard.
* مشاهدة Tickets.
* تنفيذ Next.
* تنفيذ Start.
* تنفيذ Done.
* تنفيذ Skip.
* تنفيذ Cancel.

### Future Role

**Manager / Admin**

سيتم تصميم النظام بحيث يمكن إضافة هذا الدور مستقبلًا دون إعادة بناء النظام بالكامل.

---

# 5. Core Concept — Ticket

الـTicket هي الوحدة الأساسية في QueueNow.

كل Ticket تمثل طلب خدمة/دور لمراجع معين.

تحتوي Ticket مبدئيًا على بيانات مثل:

* Ticket ID.
* Ticket Number.
* Service.
* Creation Date.
* Creation Time.
* Status.
* Timestamp information.
* Estimated Waiting Time عند الحاجة.

---

# 6. Ticket Status

يجب أن يدعم النظام الحالات التالية:

```text
Waiting
Called
Serving
Done
Skipped
Cancelled
```

## Waiting

تم إنشاء Ticket والمراجع ينتظر دوره.

## Called

تم استدعاء Ticket من قبل الموظف.

## Serving

بدأ الموظف بتقديم الخدمة للمراجع.

## Done

انتهت الخدمة بنجاح.

## Skipped

تم تجاوز Ticket ولم تتم خدمتها في ذلك الوقت.

## Cancelled

تم إلغاء Ticket.

---

# 7. Ticket Lifecycle

المسار الطبيعي للـTicket:

```text
Waiting
   ↓
Called
   ↓
Serving
   ↓
Done
```

مسارات بديلة:

```text
Waiting → Skipped
Waiting → Cancelled

Called → Skipped
Called → Cancelled
```

ويجب على النظام منع الانتقالات غير المنطقية بين الحالات.

---

# 8. Ticket Number Rules

هذه نقطة أساسية في QueueNow.

## 8.1 Ticket Persistence

لا يجب حذف الـTicket من قاعدة البيانات بعد انتهاء استخدامها.

يجب الاحتفاظ بها مع حالتها النهائية.

مثال:

```text
Ticket #12
Status: Done
```

تبقى في قاعدة البيانات.

---

## 8.2 Number Reuse

لا يجوز إعادة استخدام Ticket Number في:

* نفس اليوم
* ونفس الخدمة

حتى لو كانت الـTicket السابقة:

```text
Done
Skipped
Cancelled
```

مثال:

إذا كان آخر رقم مستخدم لخدمة معينة اليوم:

```text
15
```

فإن Ticket التالية يجب أن تكون:

```text
16
```

وليس:

```text
1
```

حتى لو كانت جميع Tickets السابقة منتهية.

---

## 8.3 Daily Numbering

يمكن أن تبدأ أرقام Tickets من جديد في يوم جديد، مع بقاء Tickets القديمة محفوظة في قاعدة البيانات.

مثال:

```text
2026-09-04 → 1, 2, 3, ... 25
2026-09-05 → 1, 2, 3, ... 
```

ويجب أن يكون الترقيم مرتبطًا أيضًا بالخدمة.

---

# 9. Services

يدعم QueueNow مفهوم **Service** لأن العيادة قد تحتوي على أكثر من نوع خدمة.

مثال:

```text
General Consultation
Dental
Laboratory
Registration
```

يجب أن تكون Ticket مرتبطة بالخدمة التي اختارها المراجع.

كما أن قواعد أرقام الـTickets تكون مستقلة لكل خدمة.

مثال:

```text
General Consultation → Ticket 15
Dental               → Ticket 8
```

---

# 10. Functional Requirements

## FR-01 — Create Ticket

يجب أن يستطيع المراجع إنشاء Ticket للخدمة المطلوبة.

عند إنشاء Ticket:

1. يحدد النظام الخدمة.
2. يحدد الرقم التالي المتاح.
3. ينشئ Ticket جديدة.
4. يخزنها في قاعدة البيانات.
5. يعرض رقم الدور للمراجع.

---

## FR-02 — View Ticket Status

يجب أن يستطيع المراجع معرفة:

* Ticket Number.
* Current Status.
* Service.
* عدد الأشخاص الموجودين قبله.
* Estimated Waiting Time.

---

## FR-03 — Queue Position

يجب أن يحسب النظام عدد Tickets التي:

* تنتمي إلى نفس الخدمة.
* حالتها الحالية ما زالت ضمن الانتظار.
* وتأتي قبل Ticket الخاصة بالمراجع.

---

## FR-04 — Employee Dashboard

يجب أن يمتلك الموظف Dashboard تعرض:

* Queue الحالية.
* Ticket Number.
* Service.
* Status.
* Creation Time.
* Current Ticket.
* Next Ticket.

---

## FR-05 — Next

يجب أن يسمح النظام للموظف بالانتقال إلى Ticket التالية المناسبة.

يتم اختيار Ticket وفق ترتيب الطابور.

---

## FR-06 — Start

عند ضغط الموظف على **Start**:

```text
Called → Serving
```

ويتم تسجيل وقت بدء الخدمة.

---

## FR-07 — Done

عند ضغط الموظف على **Done**:

```text
Serving → Done
```

ويتم تسجيل وقت انتهاء الخدمة.

---

## FR-08 — Skip

يجب أن يستطيع الموظف تجاوز Ticket.

مثال:

```text
Waiting → Skipped
```

أو بحسب حالة النظام الحالية.

ويجب الاحتفاظ بالـTicket في قاعدة البيانات.

---

## FR-09 — Cancel

يجب أن يستطيع الموظف إلغاء Ticket عند الحاجة.

مثال:

```text
Waiting → Cancelled
```

ويجب الاحتفاظ بالـTicket في قاعدة البيانات.

---

## FR-10 — Ticket History

يجب ألا تُحذف Tickets.

يجب أن يحتفظ النظام بتاريخ Tickets بما في ذلك:

* Ticket Number.
* Service.
* Status.
* Created At.
* Called At.
* Started At.
* Completed At عند توفره.

---

# 11. Estimated Waiting Time

يعد Estimated Waiting Time إحدى أهم ميزات QueueNow.

## FR-AI-01

يجب أن يوفر النظام تقديرًا تقريبيًا لمدة انتظار المراجع.

مثال:

```text
Estimated Waiting Time:
15 minutes
```

---

## FR-AI-02 — Regression Model

سيتم تنفيذ ميزة Estimated Waiting Time باستخدام **Regression Model**.

يعتمد النموذج على مجموعة Features يتم تحديدها من قبل فريق AI بعد دراسة البيانات المتاحة.

أمثلة محتملة:

* Number of people ahead.
* Average service duration.
* Current queue size.
* Time of day.
* Day of week.
* Service type.
* Historical waiting time.

**هذه الأمثلة ليست Features إلزامية**؛ يحدد فريق AI المجموعة النهائية حسب البيانات المتوفرة وجودتها.

---

## FR-AI-03

يجب أن يعرض النظام النتيجة للمستخدم بطريقة بسيطة، مثل:

```text
Estimated Waiting Time: 18 minutes
```

ويجب توضيح أن القيمة **تقديرية** وليست وقتًا مضمونًا.

---

# 12. Data Requirements

يجب أن يحتفظ النظام بالبيانات الأساسية التالية على الأقل:

### Ticket

```text
id
ticket_number
service_id
status
created_at
called_at
started_at
completed_at
```

### Service

```text
id
name
description
active
```

ويمكن إضافة حقول أخرى حسب الحاجة أثناء System Design.

---

# 13. Non-Functional Requirements

## NFR-01 — Usability

يجب أن يكون النظام سهل الاستخدام وبأقل عدد ممكن من الخطوات.

## NFR-02 — Performance

يجب أن تكون العمليات الأساسية مثل:

* Create Ticket
* View Queue
* Next
* Start
* Done

سريعة في بيئة التشغيل المستهدفة.

## NFR-03 — Reliability

يجب ألا يؤدي تحديث حالة Ticket إلى فقدان البيانات.

## NFR-04 — Data Persistence

يجب تخزين Tickets بشكل دائم في قاعدة البيانات.

## NFR-05 — Maintainability

يجب أن يكون النظام منظمًا وقابلًا للتعديل والتطوير.

## NFR-06 — Responsive Design

يجب أن تعمل واجهة النظام على أحجام الشاشات المستهدفة.

---

# 14. Security Requirements

ضمن نطاق MVP يجب تطبيق الحد الأدنى من الأمان:

* التحقق من بيانات الإدخال.
* حماية API.
* منع الوصول إلى وظائف الموظف من المستخدم غير المصرح له.
* عدم تخزين الأسرار داخل GitHub.
* استخدام Environment Variables للإعدادات الحساسة.

يمكن توسيع نظام Authentication وAuthorization في الإصدارات اللاحقة حسب الحاجة.

---

# 15. Technical Scope

الإصدار الأول يجب أن يكون نظامًا متكاملًا وليس أجزاء منفصلة.

يجب أن تعمل المنظومة بالشكل التالي:

```text
Frontend
    ↓
Backend API
    ↓
Database
    ↓
AI Model
```

ويجب أن تكون جميع المكونات متصلة وتعمل معًا.

---

# 16. Development Roles

## Frontend Team

مسؤول عن:

* Customer Interface.
* Ticket Interface.
* Queue Status.
* Employee Dashboard.
* Frontend/Backend Integration.

## Backend + Database Team

مسؤول عن:

* REST API.
* Ticket Management.
* Queue Logic.
* Ticket Number Generation.
* Status Transitions.
* Database Design.
* Database Integration.

## UI/UX Team

مسؤول عن:

* User Flow.
* Customer Interface Design.
* Employee Dashboard Design.
* Design System الأساسي.

## AI Team

مسؤول عن:

* دراسة البيانات.
* اختيار Features.
* تجهيز البيانات.
* بناء Regression Model.
* تقييم النموذج.
* توفير Prediction للـBackend.

## QA + System Analysis

مسؤول عن:

* تحليل المتطلبات.
* كتابة Test Cases.
* اختبار Workflows.
* اكتشاف المشاكل.
* التحقق من مطابقة النظام للـSRS.

## Project Management

مسؤول عن:

* التخطيط.
* توزيع Tasks.
* متابعة التقدم.
* تنظيم GitHub.
* متابعة الـDeadline.
* التنسيق بين الفرق.
* التأكد من Integration.
* متابعة اكتمال الـMVP.

---

# 17. GitHub Collaboration Requirements

جميع أعمال المشروع يجب أن تتم من خلال GitHub.

## 17.1 Contributors

يجب أن يكون أعضاء الفريق مشاركين في Repository كـContributors، وفق الصلاحيات التي يحددها مدير المشروع.

## 17.2 Tasks

يجب أن تكون لكل عضو Tasks واضحة.

مثال:

```text
FE-01 Create Ticket Page
BE-01 Create Ticket API
DB-01 Ticket Schema
AI-01 Prepare Training Dataset
QA-01 Test Ticket Lifecycle
```

## 17.3 Commits

يجب أن تكون الـCommits واضحة ومرتبطة بالعمل المنجز.

أمثلة:

```text
feat: create ticket API
feat: add employee queue dashboard
fix: prevent duplicate ticket numbers
docs: update SRS
test: add ticket lifecycle tests
```

## 17.4 Integration

لا يعتبر عمل الفريق مكتملًا إلا بعد دمج Frontend وBackend وDatabase واختباره كنظام واحد.

---


# 18. Core User Journey

يجب أن يستطيع المستخدم تنفيذ الرحلة الأساسية التالية بنجاح:

```text
Open QueueNow
      ↓
Select Service
      ↓
Take Ticket
      ↓
Receive Ticket Number
      ↓
View Ticket Status
      ↓
View People Before Me
      ↓
View Estimated Waiting Time
      ↓
Employee Calls Ticket
      ↓
Called
      ↓
Start
      ↓
Serving
      ↓
Done
```

---

# 19. MVP Acceptance Criteria

يعتبر QueueNow MVP مكتملًا عندما:

1. يستطيع المراجع الحصول على Ticket.
2. يحصل المراجع على رقم دور صحيح.
3. يتم حفظ Ticket في قاعدة البيانات.
4. يستطيع المراجع مشاهدة حالة Ticket.
5. يستطيع المراجع معرفة عدد الأشخاص قبله.
6. يستطيع الموظف مشاهدة Queue.
7. يستطيع الموظف تنفيذ Next.
8. يستطيع الموظف تنفيذ Start.
9. يستطيع الموظف تنفيذ Done.
10. يستطيع الموظف تنفيذ Skip.
11. يستطيع الموظف تنفيذ Cancel.
12. لا يتم حذف Tickets من قاعدة البيانات.
13. لا يتم إعادة استخدام Ticket Number في نفس اليوم ونفس الخدمة.
14. تعمل Frontend وBackend وDatabase معًا كنظام واحد.
15. يعمل Estimated Waiting Time باستخدام Regression Model.
16. يتم اختبار الـCore Workflow بالكامل.
17. تكون الأعمال موثقة على GitHub مع Tasks وCommits واضحة.

---

# 20. Future Enhancements

يمكن مستقبلًا إضافة:

* Manager/Admin Dashboard.
* Authentication متقدم.
* Multiple Clinics.
* Multiple Branches.
* Multiple Services.
* شاشة عرض عامة للأرقام الحالية.
* Notifications.
* SMS / WhatsApp.
* Mobile Application.
* Advanced Analytics.
* Improved Prediction Models.
* Reports.
* Priority Queue.
* Appointment Integration.

---

# 21. Conclusion

QueueNow هو Web Application يركز على إدارة الطوابير وأدوار الانتظار في العيادة.

الـMVP لا يهدف إلى إدارة العيادة طبيًا، وإنما إلى حل مشكلة أساسية ومحددة:

> **تنظيم دور المراجع وتقليل عدم وضوح وقت الانتظار من خلال نظام رقمي لإدارة الـQueue والـTickets.**

يركز الإصدار الأول على ثلاثة عناصر أساسية:

1. **Ticket Management**
2. **Queue Management**
3. **Estimated Waiting Time**

ويجب أن تكون جميع مكونات النظام مترابطة وتعمل كنظام كامل قبل اعتبار المشروع مكتملًا.
