# Tiến độ Mentor

## Module Doctrine (nền tảng cho Level 2, 3, 4, 6)

Tài liệu: `docs/doctrine/`. Package thực hành: `Packages/Application/MKintai.DoctrineLab`
(symlink → `DistributionPackages/MKintai.DoctrineLab`), độc lập với `MKintai.App`.

Môi trường đã xác nhận: PHP 8.3.6, neos/flow 9.1.2, doctrine/orm 2.20.13,
doctrine/migrations 3.9.7. Cú pháp mapping: PHP 8 Attributes.

| Bài | Nội dung | Trạng thái |
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

Ghi chú quan trọng đã kiểm chứng khi soạn module (để tham khảo khi review bài
làm sau này):

- "Aggregate root" trong Flow = có Repository riêng
  (`ClassSchema::isAggregateRoot()`). Entity không có Repository bị Flow tự
  cascade `['all']` + ép cứng `orphanRemoval=true` (không override được).
- Self-referencing ManyToMany (User follow User) BẮT BUỘC khai
  `joinColumns`/`inverseJoinColumns` tường minh trong `JoinTable`, nếu không
  join table sinh ra chỉ có 1 cột (đã gặp lỗi này thật khi soạn bài 07).
- Flow tự gọi `persistAll()` sau CẢ HTTP action (method không an toàn) VÀ
  sau khi CommandController chạy xong (`Package.php:74-101`).
- `Repository` của Flow không có `createQueryBuilder()` — phải dùng
  `$this->createQuery()->getQueryBuilder()` (alias mặc định `"e"`).

## Lộ trình 10 Level (CLAUDE.md)

Chưa bắt đầu — Level 1 sẽ khởi động sau khi module Doctrine hoàn tất (hoặc
theo lựa chọn của người học).
