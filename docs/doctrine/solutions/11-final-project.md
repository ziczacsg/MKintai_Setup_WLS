# Checklist review — Bài tổng hợp

Không có đáp án PHP sẵn cho bài này (thiết kế tự do). Dùng checklist sau để
tự review SAU KHI đã làm xong, không phải để chép trước:

## Mapping

- [ ] Mọi entity không phải aggregate root (không Repository riêng) đã được
  chủ ý để Flow auto-cascade, hoặc đã cascade tường minh nếu là
  self-referencing tới chính một aggregate root khác (bài 07).
- [ ] Mọi ManyToMany self-referencing (nếu có, ví dụ follow) đã khai
  `joinColumns`/`inverseJoinColumns` tường minh trong `JoinTable`.
- [ ] Mọi quan hệ hai chiều có method đồng bộ cả hai phía (không chỉ add
  vào 1 collection).
- [ ] Không dùng PHPDoc `Collection<int, X>` (2 type param) — chỉ
  `Collection<X>`.

## Cascade & xóa dữ liệu

- [ ] Xóa một `Post` KHÔNG xóa nhầm `Tag`/`Contributor` (chúng là aggregate
  root độc lập).
- [ ] Xóa một `Post` CÓ xóa `Comment` của nó (nếu Comment không Repository
  riêng, theo thiết kế bài 03).
- [ ] Đã quyết định có ý thức (không phải để Flow áp đặt mà không biết) cho
  MỌI cascade/orphanRemoval trong schema.

## Truy vấn

- [ ] Cả 5 truy vấn nghiệp vụ chạy đúng kết quả.
- [ ] Đã đo bằng SQL logging (`DebugStack` hoặc tương đương) rằng không có
  truy vấn nào rơi vào N+1.
- [ ] Truy vấn có `COUNT`/`GROUP BY` kèm `LIMIT` không bị lỗi nhân bản dòng
  do fetch join Collection cùng lúc.

## Hiểu biết

- [ ] Giải thích được owning side của MỌI quan hệ trong schema và lý do.
- [ ] Giải thích được vì sao mỗi entity CÓ hoặc KHÔNG CÓ Repository riêng
  (liên hệ hệ quả cascade).
