# Đáp án 10 — Truy vấn qua quan hệ

## Cơ bản: `findAllWithCommentsFetchJoin()`

```php
public function findAllWithCommentsFetchJoin(): array
{
    $queryBuilder = $this->createQuery()->getQueryBuilder();
    $queryBuilder
        ->addSelect('comments')
        ->leftJoin('e.comments', 'comments');

    return $queryBuilder->getQuery()->getResult();
}
```

Kết quả đã đo thật bằng `Doctrine\DBAL\Logging\DebugStack` (6 Post, mỗi Post
2 Comment):

```
Lazy loading (N+1): 7 queries    // 1 findAll() + 6 lazy-load riêng
Fetch join: 1 queries
```

## Nâng cao: `findTop5ByCommentCount()`

```php
public function findTop5ByCommentCount(): array
{
    $queryBuilder = $this->createQuery()->getQueryBuilder();
    $queryBuilder
        ->select('e', 'COUNT(c.id) AS commentCount')
        ->leftJoin('e.comments', 'c')
        ->groupBy('e.id')
        ->orderBy('commentCount', 'DESC')
        ->setMaxResults(5);

    return $queryBuilder->getQuery()->getResult();
}
```

Không dùng `LIMIT` (`setMaxResults`) TRÊN kết quả đã fetch-join đầy đủ dữ
liệu Comment (như `findAllWithCommentsFetchJoin`) — ở đây chỉ SELECT cột
đếm (`COUNT`), không `addSelect('c')` toàn bộ Comment, nên `GROUP BY` +
`LIMIT` cho ra đúng 5 Post, không bị nhân bản dòng.

## Thử thách

Gắn `DebugStack` khi lấy `Contributor` kèm role trên từng `Post` (bài 06):
duyệt N `Post`, mỗi lần `$post->getContributorAssignments()` lazy-load
riêng → N+1. Viết lại bằng fetch join
(`leftJoin('e.contributorAssignments', 'ca')->addSelect('ca')`) đưa về 1
query. Tương tự cho cây `Category` (bài 07): lấy toàn bộ 1 cấp con của mọi
node trong 1 query bằng `leftJoin('e.children', 'c')->addSelect('c')`, đo
số query trước/sau bằng `DebugStack` để có số liệu cụ thể thay vì chỉ đoán.

## Quiz

1. `leftJoin` chỉ điều khiển SQL JOIN ở tầng CÂU LỆNH; nếu không
   `addSelect` cột/entity phía join, Doctrine hydrate kết quả THÀNH object
   Post nhưng KHÔNG gán sẵn dữ liệu Comment vào property `$comments` — khi
   PHP code chạm `getComments()`, Doctrine coi collection đó "chưa load" và
   tự chạy thêm 1 query lazy, dù SQL ban đầu đã JOIN. "Trông như tối ưu"
   nhưng N+1 vẫn xảy ra vì hydration không nhận diện được join đó là để
   populate collection.
2. `findAllWithCommentsFetchJoin()` đã SELECT cả Comment kèm Post trong 1
   query → Doctrine hydrate và gán ngay dữ liệu Comment vào collection
   `$comments` của mỗi Post, đánh dấu collection đó "đã load đầy đủ"
   (`initialized = true`). `count()` sau đó chỉ đếm trong PHP, không hỏi
   DB. Ngược lại, `findAll()` không JOIN gì — mỗi collection còn "chưa
   khởi tạo", `count()`/foreach lần đầu buộc Doctrine tự lazy-load.
3. Vấn đề: `LIMIT 5` áp lên kết quả SQL THÔ (sau JOIN), mà 1 Post với N
   Comment sinh ra N dòng SQL (nhân bản) — `LIMIT 5` có thể cắt ngay giữa
   dữ liệu của 1 Post (chỉ lấy 3/10 Comment của nó) hoặc trả về ít hơn 5
   Post THẬT. Cách viết đúng ở bài Nâng cao: KHÔNG `addSelect` toàn bộ
   Comment entity, chỉ `COUNT()` + `GROUP BY`, để mỗi dòng SQL kết quả ứng
   với đúng 1 Post — `LIMIT 5` an toàn.
