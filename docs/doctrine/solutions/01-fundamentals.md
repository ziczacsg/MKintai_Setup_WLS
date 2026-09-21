# Đáp án 01 — Fundamentals

## Quiz

1. **Vì sao Flow không cần bạn tự khai báo cột `id`?** `PersistenceMagicAspect`
   (`Neos.Flow/Classes/Persistence/Aspect/PersistenceMagicAspect.php`) là một
   AOP Aspect với pointcut khớp mọi class `#[Flow\Entity]` hoặc
   `#[ORM\Entity]`. Nó "introduce" (chèn) thêm property
   `Persistence_Object_Identifier` (một UUID) vào class đó tại compile-time,
   trước khi Doctrine đọc mapping. Bạn không thấy property này trong source
   vì nó không tồn tại trong file bạn viết — nó chỉ tồn tại trong Proxy Class
   Flow sinh ra (`Data/Temporary/.../Cache/Code/Flow_Object_Classes/`).

2. **Chưa có trong DB.** `$repository->add($entity)` chỉ gọi
   `PersistenceManager::add()`, tức đăng ký object vào Unit of Work
   (`AbstractPersistenceManager`), KHÔNG chạy SQL. Chỉ khi
   `PersistenceManager::persistAll()` chạy, Doctrine mới tính diff và
   flush INSERT/UPDATE/DELETE thật.

3. **Có, vẫn được lưu.** Vì `Neos.Flow/Classes/Core/Booting/Package.php`
   dòng 90-99 kết nối signal `Cli\Dispatcher::afterControllerInvocation` tới
   `PersistenceManager::persistAll()` — Flow tự flush sau khi MỌI
   CommandController chạy xong, miễn PersistenceManager đã được khởi tạo
   (`hasInstance()`) và không phải compile-time. Bạn chỉ cần gọi
   `persistAll()` thủ công nếu muốn thấy hiệu ứng NGAY trong cùng method
   (ví dụ cần ID vừa insert).
