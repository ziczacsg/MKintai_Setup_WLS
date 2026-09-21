# Module: Doctrine ORM trong Flow Framework

Module nền tảng, hỗ trợ Level 2 (CRUD + migration), Level 3 (OneToMany), Level 4
(ManyToMany) và Level 6 (Rich Domain Model) trong lộ trình mentor chính
(`CLAUDE.md`). Đi qua **toàn bộ** loại quan hệ Doctrine hỗ trợ, với ví dụ chạy
thật trong package `Packages/Application/MKintai.DoctrineLab`.

## Phiên bản đã xác nhận trong môi trường này

Kiểm tra thật bằng `composer show`, `php -v`, `./flow help` — không đoán.

| Thành phần | Phiên bản |
|---|---|
| PHP | 8.3.6 |
| neos/flow | 9.1.2 |
| doctrine/orm | 2.20.13 |
| doctrine/migrations | 3.9.7 |

**Cú pháp mapping dùng trong module này: PHP 8 Attributes (`#[ORM\...]`)**, không
dùng docblock annotations (`@ORM\...`). Đã đọc source
`Neos.Flow/Classes/Reflection/ReflectionService.php` — `ReflectionService` đọc
metadata qua `ReflectionClass::getAttributes()`, nên attributes hoạt động đầy
đủ và là cách viết mới, khuyến nghị cho PHP 8.3.

**Lệnh `doctrine:*` thật có trong Flow 9.1.2** (từ `./flow help`, không đoán):

```
doctrine:validate            doctrine:create             doctrine:update
doctrine:entitystatus        doctrine:dql                doctrine:migrationstatus
doctrine:migrate             doctrine:migrationexecute   doctrine:migrationversion
doctrine:migrationgenerate
```

Không có `doctrine:status` — chỉ có `doctrine:entitystatus` (trạng thái entity)
và `doctrine:migrationstatus` (trạng thái migration).

## Cách học

1. Đọc lý thuyết trong file bài (`0N-*.md`), theo đúng thứ tự 01 → 11.
2. Mở entity starter tương ứng trong
   `backend/Packages/Application/MKintai.DoctrineLab/Classes/Domain/Model/LessonNN/`
   (symlink tới `DistributionPackages/MKintai.DoctrineLab`).
3. Điền vào các đoạn `// TODO`. Chạy `./flow doctrine:validate` để tự kiểm tra
   mapping cơ bản — Flow báo lỗi rất cụ thể (tên property + gợi ý annotation
   đúng), không cần đoán.
4. Khi mapping đã đúng: `./flow doctrine:migrationgenerate`, chọn package
   `[MKintai.DoctrineLab]` khi được hỏi di chuyển file, rồi
   `./flow doctrine:migrate` để tạo bảng thật trong MySQL (`flow_dev`).
5. Làm phần "Nâng cao" (hành vi domain) và "Thử thách" (tình huống lệch) theo
   từng bài.
6. Trả lời Quiz cuối bài. Đáp án nằm trong `solutions/NN-*.md` — **không mở
   trước khi tự làm xong**.

## Package thực hành

`Packages/Application/MKintai.DoctrineLab` — package Flow riêng, không đụng
vào package blog chính (`MKintai.App`). Mỗi bài có namespace riêng
(`MKintai\DoctrineLab\Domain\Model\LessonNN`) và bảng riêng
(`lessonNN_*`) để không đụng lẫn nhau.

## Bảng tiến độ

| # | Bài | Trạng thái |
|---|---|---|
| 01 | Fundamentals (Entity, Repository, PersistenceManager) | Chưa bắt đầu |
| 02 | ManyToOne một chiều | Chưa bắt đầu |
| 03 | OneToMany hai chiều | Chưa bắt đầu |
| 04 | OneToOne | Chưa bắt đầu |
| 05 | ManyToMany | Chưa bắt đầu |
| 06 | ManyToMany có thuộc tính phụ (entity trung gian) | Chưa bắt đầu |
| 07 | Self-referencing (cây, follow) | Chưa bắt đầu |
| 08 | Cascade, orphanRemoval, fetch mode | Chưa bắt đầu |
| 09 | Inheritance & Value Object | Chưa bắt đầu |
| 10 | Truy vấn qua quan hệ (DQL, N+1) | Chưa bắt đầu |
| 11 | Bài tổng hợp | Chưa bắt đầu |

## Ghi chú quan trọng đã kiểm chứng bằng source + chạy thật (đọc trước khi bắt đầu)

- **"Aggregate root" trong Flow = có Repository riêng.** `ClassSchema::isAggregateRoot()`
  (`Neos.Flow/Classes/Reflection/ClassSchema.php:226`) trả về `true` khi và chỉ
  khi entity có class Repository tương ứng. Điều này ảnh hưởng cascade/orphanRemoval
  mặc định — xem bài 03 và 08.
- Mọi entity Flow (`#[Flow\Entity]` hay cả `#[ORM\Entity]` thuần) đều được
  `PersistenceMagicAspect` tự động gắn thêm cột khóa chính
  `persistence_object_identifier VARCHAR(40)` qua AOP — bạn không tự khai báo
  cột này, và không dễ tắt được (xem bài 04, phần OneToOne dùng khóa chính chung).
- `Neos.Flow/Classes/Core/Booting/Package.php` (dòng 74-101) cho thấy Flow tự
  gọi `PersistenceManager::persistAll()` sau **cả** HTTP action (method không an
  toàn) **và** sau khi một CommandController chạy xong — khác Laravel/Eloquent
  vốn lưu ngay khi gọi `->save()`.
