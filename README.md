# Liveness Server

خادم بديل لـ `blsbls.shop` لتشغيل خاصية التحقق (liveness).

## المسارات (Endpoints)
- **GET** `/retrieve_data.php?user_id=...`
  - يعيد بيانات المستخدم المطلوبة لبدء التحقق.
- **POST** `/update_liveness.php`
  - يستقبل نتيجة التحقق ويحفظها.

## التشغيل على Render أو أي استضافة PHP
1. ارفع الملفات الأربعة (`index.php`, `retrieve_data.php`, `update_liveness.php`, `README.md`).
2. عدل على `retrieve_data.php` و `update_liveness.php` لربطهم بقاعدة بياناتك.
3. غير كل الروابط في الإضافة من:
