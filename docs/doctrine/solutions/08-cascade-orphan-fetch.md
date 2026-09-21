# Đáp án 08 — Cascade, orphanRemoval, fetch mode

## Cơ bản: mapping

```php
// Order.php
#[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, fetch: 'EXTRA_LAZY')]
protected Collection $items;

// OrderItem.php
#[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
#[ORM\JoinColumn(name: 'order_ref', referencedColumnName: 'persistence_object_identifier', nullable: false)]
protected Order $order;

// Invoice.php — quyết định tường minh: cascade persist, KHÔNG orphanRemoval
#[ORM\OneToMany(mappedBy: 'invoice', targetEntity: InvoiceLine::class, cascade: ['persist'], orphanRemoval: false)]
protected Collection $lines;

// InvoiceLine.php — DB-level cascade
#[ORM\ManyToOne(targetEntity: Invoice::class, inversedBy: 'lines')]
#[ORM\JoinColumn(name: 'invoice', referencedColumnName: 'persistence_object_identifier', nullable: false, onDelete: 'CASCADE')]
protected Invoice $invoice;
```

Lý do chọn `cascade: ['persist'], orphanRemoval: false` cho `Invoice::$lines`:
`InvoiceLine` có Repository riêng → được coi là chứng từ độc lập, có thể
cần giữ lại (ví dụ cho mục đích kế toán/audit) dù bị gỡ khỏi một Invoice cụ
thể — khác `OrderItem` (không Repository) nơi việc gỡ khỏi Order đồng nghĩa
"không còn cần tồn tại nữa".

## Kết quả đã chạy thật

```
lesson08_order_item rows after removeItem() (no Repository, forced orphanRemoval): 0
lesson08_invoice_line rows after removeLine() (has Repository, orphanRemoval:false honoured): 1
```

## Nâng cao: EXTRA_LAZY không load toàn bộ collection

```php
$order = new Order('ORD-1');
for ($i = 0; $i < 5; $i++) {
    $order->addItem(new OrderItem("Item $i", $order));
}
$this->orderRepository->add($order);
$this->persistenceManager->persistAll();

$this->entityManager->clear();
$order = $this->orderRepository->findAll()->getFirst();

$logger = new \Doctrine\DBAL\Logging\DebugStack();
$this->entityManager->getConnection()->getConfiguration()->setSQLLogger($logger);
$count = $order->getItems()->count();   // EXTRA_LAZY -> chạy SELECT COUNT(*), không load 5 item
// $logger->queries cuối cùng sẽ chứa đúng 1 câu SELECT COUNT(*) FROM lesson08_order_item WHERE order_ref = ...
```

## Thử thách

`onDelete: 'CASCADE'` ở DB áp dụng cho MỌI hàng có FK `invoice` trỏ tới
Invoice bị xóa — **bất kể** hàng đó có đang nằm trong collection PHP nào
hay không, và bất kể `orphanRemoval` ORM là gì. Vì vậy: có, xóa Invoice sẽ
xóa luôn `InvoiceLine` "mồ côi" đó ở tầng MySQL, dù ORM cascade
(`orphanRemoval: false`) đã cố tình giữ nó lại trước đó. Đây chính xác là
điểm cần hiểu: `onDelete` ở DB là lớp bảo vệ CUỐI CÙNG, độc lập hoàn toàn
với ý định ở tầng ORM.

## Quiz

1. Đọc đúng `FlowAnnotationDriver.php:622-628`: với `orphanRemoval`, nhánh
   `if (isAggregateRoot() === false && !isValueObject())` được kiểm tra
   TRƯỚC và ép `true` ngay, khiến `elseif ($annotation->orphanRemoval !==
   null)` (nơi đọc giá trị bạn khai) không bao giờ chạy khi target không
   phải aggregate root. Với `cascade` thì ngược lại: `if
   ($annotation->cascade !== null)` được kiểm tra TRƯỚC TIÊN, nên giá trị
   bạn khai luôn thắng, bất kể aggregate root hay không.
2. `onDelete: 'CASCADE'` quan trọng hơn khi hệ thống có thao tác XÓA DỮ LIỆU
   HÀNG LOẠT bằng SQL trực tiếp (migration dọn dữ liệu cũ, script batch) —
   ORM cascade không bao giờ chạy trong trường hợp đó. `cascade` ORM quan
   trọng hơn khi bạn cần logic PHP chạy kèm (ví dụ observer, event, hoặc
   cascade `persist` để tự lưu entity con mới tạo — DB `onDelete` không giúp
   được gì cho INSERT).
3. Chọn `EXTRA_LAZY` khi collection có thể lên tới hàng trăm/nghìn phần tử
   VÀ code thường chỉ cần biết SỐ LƯỢNG hoặc KIỂM TRA TỒN TẠI một phần tử cụ
   thể (`count()`, `contains()`), không cần duyệt toàn bộ. Nếu code luôn
   `foreach` toàn bộ collection ngay sau khi load, `EXTRA_LAZY` không giúp
   gì (vẫn phải load hết khi foreach) — nên dùng `LAZY` thường hoặc fetch
   join (bài 10).
