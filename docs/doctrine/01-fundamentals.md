# 01 — Fundamentals: Entity, Repository, PersistenceManager

## Mục tiêu

Hiểu 3 khái niệm lõi của Doctrine trong Flow trước khi đụng tới quan hệ:
Entity, Repository, PersistenceManager — và thời điểm dữ liệu thật sự được
ghi xuống DB.

## Lý thuyết

**Entity** là một class PHP thường, được đánh dấu `#[Flow\Entity]`. Flow dùng
AOP (`PersistenceMagicAspect`) để tự động thêm một cột khóa chính
`persistence_object_identifier VARCHAR(40)` (một UUID) vào MỌI entity — bạn
không tự khai báo `id`. Đã xác nhận bằng cách generate migration thật:

```sql
CREATE TABLE lesson01_note (
    persistence_object_identifier VARCHAR(40) NOT NULL,
    title VARCHAR(120) NOT NULL,
    body LONGTEXT NOT NULL,
    PRIMARY KEY(persistence_object_identifier)
) ENGINE = InnoDB
```

**Repository** là nơi duy nhất bạn `add()`/`remove()`/`update()` một Aggregate
Root. Trong Flow, một entity được coi là **Aggregate Root** khi nó có một
Repository riêng theo convention `Domain\Model\X` → `Domain\Repository\XRepository`
(hoặc khai báo tường minh qua `repositoryClassName`). Điều này được đọc trực
tiếp từ source:

```php
// Neos.Flow/Classes/Reflection/ClassSchema.php:226
public function isAggregateRoot() {
    return $this->repositoryClassName !== null;
}
```

Ghi nhớ chính xác này — nó quyết định cascade/orphanRemoval mặc định ở bài 03
và 08.

**PersistenceManager** quản lý Unit of Work (danh sách object cần
insert/update/delete) và quyết định KHI NÀO thật sự chạy SQL, qua
`persistAll()`.

## Owning side vs Inverse side

Chưa áp dụng ở bài này (không có quan hệ), nhưng khái niệm sẽ xuất hiện từ bài
02: bên "owning" là bên có cột khóa ngoại (FK) trong DB và là bên Doctrine đọc
để quyết định ghi gì.

## Cách Flow làm khác Laravel/Eloquent

Trong Laravel, `$post->save()` ghi DB **ngay lập tức**. Trong Flow, gọi
`$repository->add($entity)` chỉ **đăng ký** entity vào Unit of Work — dữ liệu
CHƯA vào DB. Đọc source `Neos.Flow/Classes/Core/Booting/Package.php` (dòng
74-101) cho thấy Flow tự động gọi `PersistenceManager::persistAll()`:

- sau khi một HTTP Controller action chạy xong, NẾU request dùng method không
  an toàn (POST/PUT/DELETE...).
- sau khi một CLI CommandController chạy xong (bất kể method gì).

Vì vậy trong Controller/Command bạn **hầu như không cần gọi `persistAll()` thủ
công** — nó tự chạy ở cuối request/command. Bạn CHỈ cần gọi thủ công khi muốn
lấy dữ liệu đã insert NGAY trong cùng method (ví dụ cần ID vừa insert để dùng
tiếp), hoặc muốn bắt lỗi validate DB sớm.

## Ví dụ chạy được

Entity mẫu (đã chạy `doctrine:validate` + `doctrine:migrationgenerate` +
`doctrine:migrate` thật, xem SQL ở trên):

```php
// Classes/Domain/Model/Lesson01/Note.php
#[Flow\Entity]
#[ORM\Table(name: 'lesson01_note')]
class Note
{
    #[ORM\Column(type: 'string', length: 120)]
    protected string $title;

    #[ORM\Column(type: 'text')]
    protected string $body;
    // ...constructor, getters
}
```

```php
// Classes/Domain/Repository/Lesson01/NoteRepository.php
#[Flow\Scope('singleton')]
class NoteRepository extends Repository
{
}
```

## Bẫy thường gặp

1. Quên `#[Flow\Scope('singleton')]` trên Repository — Flow vẫn hoạt động
   (Repository mặc định singleton) nhưng nên khai báo rõ để tránh nhầm với
   entity (luôn `prototype`).
2. Tưởng `$repository->add()` đã lưu vào DB ngay — không, phải chờ
   `persistAll()` (tự động hoặc gọi tay).
3. Đặt Entity là Value Object thay vì Entity (nhầm `#[Flow\ValueObject]` với
   `#[Flow\Entity]`) — Value Object không có identity riêng theo cách bạn nghĩ
   (xem bài 09).
4. Quên `declare(strict_types=1)` + type hint đúng — Flow đọc property type
   qua Reflection để suy luận cột DB, sai type sẽ sai cột.

## Bài tập

Không có starter TODO riêng cho bài này — `Note` đã hoàn chỉnh để bạn đọc và
chạy thử. Bài tập nhỏ (không bắt buộc, không có trong `solutions/`): thêm
method `updateBody(string $body): void` vào `Note` và gọi thử qua một
CommandController tạm để quan sát `persistAll()` tự động chạy khi command kết
thúc.

## Cách tự kiểm tra

```bash
./flow doctrine:validate          # mapping OK
./flow doctrine:migrationgenerate # xem SQL sinh ra có khớp phần lý thuyết trên
```

## Quiz

1. Vì sao Flow không cần bạn tự khai báo cột `id`? Cơ chế nào chèn cột đó vào?
2. `$repository->add($entity)` xong, chưa gọi `persistAll()`, entity đã có
   trong DB chưa? Vì sao?
3. Nếu bạn viết một CommandController gọi `$repository->add($x)` rồi kết thúc
   method mà không gọi `persistAll()` thủ công, dữ liệu có được lưu không? Vì
   sao (liên hệ `Package.php` dòng 90-99)?

Đáp án: `solutions/01-fundamentals.md`.
