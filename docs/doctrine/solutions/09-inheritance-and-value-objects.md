# Đáp án 09 — Inheritance & Value Object

## Cơ bản: mapping

```php
// Notification.php
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string', length: 20)]
#[ORM\DiscriminatorMap(['email' => EmailNotification::class, 'sms' => SmsNotification::class])]

// Payment.php
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string', length: 20)]
#[ORM\DiscriminatorMap(['card' => CardPayment::class, 'bank_transfer' => BankTransferPayment::class])]

// Address.php
#[Flow\ValueObject]
```

## Nâng cao: thêm `CashPayment`

```php
#[Flow\Entity]
#[ORM\Table(name: 'lesson09_cash_payment')]
class CashPayment extends Payment
{
    public function __construct(int $amountInCents)
    {
        parent::__construct($amountInCents);
    }
}
```

Cập nhật `DiscriminatorMap` thêm `'cash' => CashPayment::class`. Bảng
`lesson09_cash_payment` sinh ra chỉ có `persistence_object_identifier` (FK
tới `lesson09_payment`, `ON DELETE CASCADE`) — không cột nghiệp vụ nào khác,
vì mọi dữ liệu (`amountInCents`) đã nằm ở bảng cha.

## Thử thách

Đổi `Notification` sang `JOINED` sinh thêm 2 bảng
(`lesson09_email_notification`, `lesson09_sms_notification`), mỗi bảng chỉ
có cột riêng (`emailaddress`/`phonenumber`) + FK về `lesson09_notification`.
Đổi `Payment` sang `SINGLE_TABLE` gộp `amountincents`, `lastfourdigits`,
`iban` vào MỘT bảng `lesson09_payment`, `lastfourdigits`/`iban` đều
`DEFAULT NULL`.

Tiêu chí chọn: **Single Table** khi hierarchy có NHIỀU subclass nhưng MỖI
subclass chỉ thêm RẤT ÍT cột riêng (như `Notification` — chỉ 1 cột khác biệt
mỗi loại), và bạn thường query TOÀN BỘ hierarchy cùng lúc (danh sách mọi
Notification, không phân biệt loại) — tránh JOIN. **Joined Table** khi
subclass có NHIỀU cột riêng, khác biệt lớn về schema (như `Payment` —
`CardPayment` có thể có nhiều cột thẻ, `BankTransferPayment` nhiều cột ngân
hàng khác hẳn), và bạn cần ràng buộc NOT NULL chặt cho cột riêng từng loại
(chỉ làm được khi có bảng riêng).

## Quiz

1. Bảng con trong Joined Table Inheritance dùng CHÍNH
   `persistence_object_identifier` của bảng cha làm cả PK và FK — về bản
   chất chúng là "phần mở rộng" của CÙNG MỘT row logic, nên xóa cha luôn kéo
   theo phần mở rộng biến mất (không có ý nghĩa gì để nó tồn tại riêng lẻ).
   Quan hệ ManyToOne thường (Comment→Post, bài 02) là 2 entity ĐỘC LẬP về
   mặt vòng đời — mặc định không có lý do gì để xóa cha kéo theo xóa con.
2. Với `embedded: false`: `Address` có bảng riêng + `persistence_object_identifier`
   riêng; Flow tự DEDUP — hai `Customer` có `Address` giống nhau (cùng
   street/zip/city) có thể dùng CHUNG một row `Address` trong DB (theo docblock
   gốc của `Flow\ValueObject`: "store only one instance of the value
   object"), thay vì mỗi Customer có bản copy riêng như khi `embedded: true`.
3. Tiêu chí cụ thể: nếu số subclass ≥ 4-5 VÀ mỗi subclass chỉ thêm ≤ 2-3 cột
   riêng VÀ query phổ biến nhất là lấy TOÀN BỘ hierarchy không lọc theo
   loại → Single Table. Nếu số subclass nhỏ (2-3) NHƯNG mỗi subclass có
   ≥ 5 cột riêng biệt, ít khi query chung cả hierarchy → Joined Table.
