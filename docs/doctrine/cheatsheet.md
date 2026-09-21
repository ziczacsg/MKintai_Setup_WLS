# Cheatsheet: Doctrine ORM trong Flow 9.1.2

## Bảng tra nhanh các loại quan hệ

| Quan hệ | Attribute owning side | Attribute inverse side | FK nằm ở bảng nào |
|---|---|---|---|
| ManyToOne 1 chiều | `#[ORM\ManyToOne]` + `#[ORM\JoinColumn]` | (không có) | Bảng "nhiều" |
| OneToMany 2 chiều | `#[ORM\ManyToOne(inversedBy:)]` + `JoinColumn` (phía "nhiều") | `#[ORM\OneToMany(mappedBy:)]` (phía "một") | Bảng "nhiều" |
| OneToOne | `#[ORM\OneToOne(inversedBy:)]` + `JoinColumn(unique:true)` | `#[ORM\OneToOne(mappedBy:)]` | Bảng owning (tùy chọn) |
| ManyToMany | `#[ORM\ManyToMany(inversedBy:)]` + `#[ORM\JoinTable]` | `#[ORM\ManyToMany(mappedBy:)]` | Join table riêng |
| ManyToMany + thuộc tính phụ | Tách thành entity trung gian, 2× `#[ORM\ManyToOne]` | — | Bảng entity trung gian (có PK riêng) |
| Self-referencing 1-N (cây) | `#[ORM\ManyToOne(targetEntity: self::class, inversedBy:)]` | `#[ORM\OneToMany(mappedBy:)]` | Chính bảng đó |
| Self-referencing N-N | Như ManyToMany, nhưng **PHẢI** khai `joinColumns`/`inverseJoinColumns` tường minh trong `JoinTable` | — | Join table, 2 cột tên khác nhau |

## Aggregate root & cascade mặc định (Flow-specific, đã verify bằng source + chạy thật)

```
isAggregateRoot(EntityX) === true  <=>  tồn tại XRepository (class Repository cho X)
```

| Target có Repository? | cascade mặc định (nếu không khai) | orphanRemoval |
|---|---|---|
| Không (không phải aggregate root) | `['all']` | **Luôn `true`, không override được** dù bạn khai `orphanRemoval: false` |
| Có (là aggregate root) | Không tự thêm gì | Tôn trọng giá trị bạn khai (mặc định `false` nếu không khai) |
| Target là `#[Flow\ValueObject]` | `['persist']` | Tôn trọng giá trị bạn khai |

Nguồn: `Neos.Flow/Classes/Persistence/Doctrine/Mapping/Driver/FlowAnnotationDriver.php:614-628`.

## onDelete (DB) vs cascade (ORM)

| | Kích hoạt khi | Bỏ qua Doctrine (raw SQL) vẫn hoạt động? |
|---|---|---|
| `#[ORM\JoinColumn(onDelete: 'CASCADE')]` | MySQL FK constraint | **Có** |
| `cascade: ['remove']` trên OneToMany/OneToOne | `$repository->remove()` + `persistAll()` | Không |

## Fetch mode

| Mode | `count()`/`contains()` | Khi nào dùng |
|---|---|---|
| `LAZY` (mặc định) | Load cả collection rồi đếm trong PHP | Collection nhỏ, hay cần load toàn bộ |
| `EXTRA_LAZY` | `SELECT COUNT(*)` / `SELECT ... WHERE` riêng | Collection có thể rất lớn, chỉ cần đếm/kiểm tra tồn tại |
| `EAGER` | Load ngay khi load entity cha | Hiếm dùng cho `*ToMany`, cẩn thận N+1 ngược (load thừa) |

## Inheritance

| Chiến lược | Số bảng | Khi dùng |
|---|---|---|
| `SINGLE_TABLE` | 1 bảng cho cả hierarchy | Ít cột riêng mỗi subclass, hay query toàn bộ hierarchy |
| `JOINED` | 1 bảng cha + 1 bảng/subclass, FK = PK cha, tự `ON DELETE CASCADE` | Nhiều cột riêng biệt, subclass đủ khác nhau |

## Value Object

```php
#[Flow\ValueObject]                    // embedded=true (default): inline columns, KHÔNG bảng riêng
#[Flow\ValueObject(embedded: false)]   // CÓ bảng + identity riêng, Flow tự dedup theo giá trị
```

## Lệnh doctrine:* (Flow 9.1.2 — tên đã xác nhận, không phải Symfony/Doctrine gốc)

```
doctrine:validate            doctrine:create             doctrine:update
doctrine:entitystatus        doctrine:dql                doctrine:migrationstatus
doctrine:migrate [--version] doctrine:migrationexecute   doctrine:migrationversion
doctrine:migrationgenerate
```

## Fetch join qua Flow Repository (không phải Doctrine EntityRepository thuần)

```php
// Repository::createQueryBuilder() KHÔNG tồn tại trong Flow. Dùng:
$queryBuilder = $this->createQuery()->getQueryBuilder();  // alias mặc định: "e"
$queryBuilder->addSelect('comments')->leftJoin('e.comments', 'comments');
return $queryBuilder->getQuery()->getResult();
```

## PHPDoc generic Flow hiểu được

```php
/** @var Collection<Comment> */   // OK — Flow's TypeHandling chỉ hỗ trợ 1 type param
/** @var Collection<int, Comment> */   // LỖI: "InvalidArgumentException: Found an invalid element type declaration"
```

## Identity

Mọi Entity (Flow hay Doctrine attribute thuần) được `PersistenceMagicAspect`
tự gắn `persistence_object_identifier VARCHAR(40)` làm PK qua AOP — không tự
khai `id`, và không có cách "tắt" để dùng shared primary key kiểu Doctrine
cổ điển.
