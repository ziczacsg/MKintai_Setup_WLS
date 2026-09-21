# 08 — Cascade, orphanRemoval, fetch mode

## Mục tiêu

Hiểu chính xác cascade/orphanRemoval mặc định của Flow (giới thiệu ở bài 03)
có thể **override được hay không** tùy trường hợp, sự khác biệt giữa cascade
ở tầng ORM và `onDelete` ở tầng DB, và 3 fetch mode LAZY/EAGER/EXTRA_LAZY.

## Lý thuyết: hai cặp entity, một câu hỏi

| Cặp | Con có Repository riêng? | Là aggregate root? |
|---|---|---|
| `Order` → `OrderItem` | Không | Không |
| `Invoice` → `InvoiceLine` | **Có** | **Có** |

Cả hai đều khai `OneToMany` từ cha xuống con. Câu hỏi: cascade/orphanRemoval
có giống nhau không?

## Kết quả thật (đã chạy bằng CommandController tạm trong lúc soạn bài, log dưới đây là output thật)

```
lesson08_order_item rows after removeItem() (no Repository, forced orphanRemoval): 0
lesson08_invoice_line rows after removeLine() (has Repository, orphanRemoval:false honoured): 1
```

**`OrderItem`** (không Repository): dù bạn khai gì hay không khai gì cho
`orphanRemoval`, Flow **ép cứng `orphanRemoval = true`**. Đọc đúng dòng
source quyết định điều này —
`Neos.Flow/Classes/Persistence/Doctrine/Mapping/Driver/FlowAnnotationDriver.php:622-628`:

```php
// We need to apply our value for non-aggregate roots first, because Doctrine
// sets a default value for orphanRemoval (see #1127)
if ($this->isAggregateRoot($mapping['targetEntity'], $className) === false &&
    $this->isValueObject($mapping['targetEntity'], $className) === false) {
    $mapping['orphanRemoval'] = true;                              // <-- LUÔN true
} elseif ($oneToManyAnnotation->orphanRemoval !== null) {
    $mapping['orphanRemoval'] = $oneToManyAnnotation->orphanRemoval; // chỉ áp dụng nếu KHÔNG rơi vào nhánh trên
}
```

Chú ý dòng comment ngay trong source: **cố tình** ưu tiên nhánh ép cứng
trước. Nghĩa là dù bạn viết `orphanRemoval: false` trên property của một
entity con KHÔNG có Repository, giá trị đó **bị bỏ qua hoàn toàn**.

**`InvoiceLine`** (CÓ Repository = aggregate root): nhánh ép cứng không áp
dụng, `elseif` phía dưới chạy → giá trị bạn khai (`orphanRemoval: false`)
được tôn trọng. Kết quả thật: xóa `InvoiceLine` khỏi collection rồi
`persistAll()` — row VẪN CÒN trong DB (chỉ mất liên kết `invoice`, không bị
xóa) — đúng như khai báo.

Với `cascade` (không phải `orphanRemoval`), logic khác một chút: giá trị bạn
khai LUÔN được ưu tiên đọc trước (`if ($annotation->cascade !== null)`), chỉ
rơi về mặc định `['all']` khi bạn không khai gì VÀ target không phải
aggregate root.

## Cách Flow làm khác Laravel/Eloquent

Laravel: cascade delete phải khai tường minh (`onDelete('cascade')` ở
migration, hoặc observer). Flow: cascade delete có thể **tự động xảy ra**
nếu bạn không để ý entity con có Repository hay không — đây là nguồn bug
kinh điển: xóa nhầm dữ liệu con vì tưởng nó "độc lập" nhưng nó không có
Repository riêng.

## DB-level `onDelete` vs ORM-level cascade — khác nhau thật

```php
// InvoiceLine.php
#[ORM\ManyToOne(targetEntity: Invoice::class, inversedBy: 'lines')]
#[ORM\JoinColumn(name: 'invoice', referencedColumnName: 'persistence_object_identifier', nullable: false, onDelete: 'CASCADE')]
protected Invoice $invoice;
```

SQL sinh ra thật:
```sql
ALTER TABLE lesson08_invoice_line ADD CONSTRAINT FK_97ACF8C490651744
    FOREIGN KEY (invoice) REFERENCES lesson08_invoice (persistence_object_identifier) ON DELETE CASCADE
```

`onDelete: 'CASCADE'` là quy tắc ở **tầng MySQL**: nếu ai đó chạy
`DELETE FROM lesson08_invoice WHERE ...` bằng SQL thô (bypass hoàn toàn
Doctrine/EntityManager), MySQL vẫn tự xóa các `InvoiceLine` liên quan. Cascade
ORM (`cascade: ['remove']`) chỉ hoạt động khi bạn đi qua
`$invoiceRepository->remove()` + `persistAll()`. Hai cơ chế **độc lập, nên
dùng cả hai** khi thực sự muốn dữ liệu con luôn bị xóa theo cha trong mọi
trường hợp.

## Fetch mode

`Order::$items` khai `fetch: 'EXTRA_LAZY'`:

```php
#[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, fetch: 'EXTRA_LAZY')]
protected Collection $items;
```

- `LAZY` (mặc định): gọi `$order->getItems()` không query gì, nhưng
  `count()`/`contains()`/foreach đều load TOÀN BỘ collection vào memory rồi
  đếm/duyệt trong PHP.
- `EXTRA_LAZY`: `count()` chạy `SELECT COUNT(*)`, `contains($x)` chạy
  `SELECT ... WHERE ...` — không load cả collection. Quan trọng khi
  collection có thể rất lớn (một Order với 10,000 item, chỉ cần biết có bao
  nhiêu item).
- `EAGER`: load collection ngay khi load `Order` (JOIN hoặc query phụ ngay
  lập tức) — hiếm dùng cho `OneToMany`, dễ gây tải dữ liệu không cần thiết.

## Bẫy thường gặp

1. Đặt `orphanRemoval: false` trên quan hệ tới entity KHÔNG có Repository,
   tưởng đã tắt được — Flow âm thầm bỏ qua, dữ liệu vẫn bị xóa.
2. Dùng `onDelete: 'CASCADE'` ở DB nhưng quên rằng ORM cascade KHÔNG tự động
   theo — xóa qua Repository mà quên cascade ORM vẫn có thể lỗi FK nếu thứ
   tự xóa sai (phải xóa con trước cha nếu không có `onDelete`).
3. Dùng `fetch: 'EAGER'` cho collection lớn → load toàn bộ dữ liệu không cần
   thiết mỗi khi load entity cha, kể cả khi bạn chỉ cần `$order->getReference()`.
4. Tưởng `EXTRA_LAZY` tự áp dụng cho mọi method — chỉ áp dụng cho
   `count()`, `contains()`, `slice()`; gọi `->toArray()` hay foreach vẫn load
   hết.
5. Cascade `['remove']` trên quan hệ tới aggregate root (entity có Repository
   riêng, ví dụ nếu lỡ thêm vào `Invoice::$lines`) — về mặt DDD thường SAI:
   xóa cha không nên tự xóa một aggregate root khác.

## Bài tập

- **Cơ bản**: điền TODO cho `Order::$items` (OneToMany + fetch EXTRA_LAZY),
  `OrderItem::$order` (ManyToOne), `Invoice::$lines` (OneToMany — tự quyết
  định cascade/orphanRemoval), `InvoiceLine::$invoice` (ManyToOne + DB
  `onDelete`).
- **Nâng cao**: viết đoạn code tạo `Order` với 5 `OrderItem`, chỉ
  `$orderRepository->add($order)`, sau đó dùng `EXTRA_LAZY` để in ra số
  lượng item KHÔNG load toàn bộ collection (`$order->getItems()->count()`),
  và bật SQL logging để chứng minh nó chạy `SELECT COUNT(*)` chứ không
  `SELECT *`.
- **Thử thách**: xóa một `Invoice` còn `InvoiceLine` đã bị `removeLine()`
  (còn "mồ côi" trong DB, do `orphanRemoval: false`). `onDelete: 'CASCADE'`
  ở DB có xóa nốt `InvoiceLine` mồ côi đó không? Kiểm chứng bằng SQL thật.

## Cách tự kiểm tra

```bash
./flow doctrine:validate
./flow doctrine:migrationgenerate && ./flow doctrine:migrate
```
```sql
SELECT COUNT(*) FROM lesson08_order_item;   -- sau removeItem(): phải là 0 nếu Order.items cascade đúng
SELECT COUNT(*) FROM lesson08_invoice_line; -- sau removeLine(): phải giữ nguyên (orphaned) nếu bạn khai orphanRemoval:false
```

## Quiz

1. Vì sao `orphanRemoval: false` khai trên `Order::$items` không có tác
   dụng gì, nhưng khai y hệt trên `Invoice::$lines` lại có? Chỉ đúng dòng
   logic trong `FlowAnnotationDriver` quyết định điều này.
2. Cho một ví dụ cụ thể (không phải Order/Invoice) nơi `onDelete: 'CASCADE'`
   ở DB QUAN TRỌNG hơn cascade ORM, và một ví dụ ngược lại.
3. Khi nào bạn nên chọn `EXTRA_LAZY` thay vì `LAZY` cho một collection? Cho
   một tiêu chí cụ thể (không phải "khi collection lớn" — nói rõ lớn cỡ nào,
   dùng vào việc gì).

Đáp án: `solutions/08-cascade-orphan-fetch.md`.
