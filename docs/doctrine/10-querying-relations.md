# 10 — Truy vấn qua quan hệ: DQL, QueryBuilder, N+1, fetch join

## Mục tiêu

Nhận diện và đo được N+1 query bằng SQL logging thật (không chỉ đọc lý
thuyết), rồi giải quyết bằng fetch join qua Flow's `QueryBuilder`.

## Lý thuyết

`Post` (bài 10) có nhiều `Comment` (giống bài 03). Khi bạn:

```php
$posts = $postRepository->findAll();
foreach ($posts as $post) {
    $post->getComments()->count();   // LAZY -> query riêng cho MỖI Post
}
```

Mỗi lần chạm `getComments()` trên một Post CHƯA từng load Comment trong
request này, Doctrine chạy thêm 1 SQL query. Với N Post → N+1 query (1 cho
`findAll()`, N cho từng Post).

## Đo N+1 thật bằng SQL logging (đã chạy)

Dùng `Doctrine\DBAL\Logging\DebugStack` gắn vào Connection, tạo 6 Post (mỗi
Post 2 Comment), rồi:

```
Lazy loading (N+1): 7 queries
Fetch join: 1 queries
```

`7 = 1 (findAll) + 6 (một lazy-load riêng cho mỗi Post)`. Với fetch join,
đúng **1 query duy nhất** cho toàn bộ Post + Comment.

## Fetch join bằng Flow QueryBuilder

Flow's `Repository::createQuery()` trả về `Neos\Flow\Persistence\Doctrine\Query`
— **không phải** Doctrine's `EntityRepository` trực tiếp, nên không có
`createQueryBuilder()` (đã thử, báo lỗi
`Call to undefined method ...PostRepository::createQueryBuilder`). Cách đúng
là lấy QueryBuilder từ bên trong Flow Query:

```php
public function findAllWithCommentsFetchJoin(): array
{
    $queryBuilder = $this->createQuery()->getQueryBuilder();
    // alias mặc định của Flow Query là "e" (Neos.Flow/Classes/Persistence/Doctrine/Query.php:136)
    $queryBuilder
        ->addSelect('comments')
        ->leftJoin('e.comments', 'comments');

    return $queryBuilder->getQuery()->getResult();
}
```

`addSelect('comments')` là bước dễ quên nhất: thiếu nó, `leftJoin` vẫn chạy
nhưng Doctrine không SELECT dữ liệu Comment kèm theo → vẫn lazy-load riêng
khi bạn chạm `getComments()`, dù đã JOIN ở SQL. Phải có CẢ `leftJoin` VÀ
`addSelect` mới thành fetch join thật.

## DQL tương đương (dùng `./flow doctrine:dql` để thử trực tiếp)

```bash
./flow doctrine:dql "SELECT p, c FROM MKintai\DoctrineLab\Domain\Model\Lesson10\Post p LEFT JOIN p.comments c"
```

## Owning side vs Inverse side

Không có khái niệm mới — quan hệ `Post`↔`Comment` ở bài này giống bài 03
(OneToMany hai chiều, owning là `Comment::$post`). Điểm khác là TRỌNG TÂM
bài học chuyển từ "mapping đúng" sang "query hiệu quả".

## Cách Flow làm khác Laravel/Eloquent

Laravel: `Post::with('comments')->get()` — eager loading khai báo bằng tên
string, Laravel tự query 2 lần (1 cho Post, 1 `WHERE post_id IN (...)` cho
Comment) — **không phải 1 query JOIN**, mà 2 query tối ưu. Flow/Doctrine
fetch join dùng SQL `JOIN` thật, **1 query duy nhất** — khác về cơ chế dù
cùng mục tiêu giảm N+1. Đánh đổi: JOIN có thể trả về dữ liệu trùng lặp ở
tầng SQL (1 row Post × N Comment) mà Doctrine tự "hydrate" lại thành đúng 1
object Post với N Comment trong PHP — Laravel's cách 2-query tránh được
việc này nhưng tốn round-trip DB nhiều hơn.

## Bẫy thường gặp

1. `leftJoin` mà quên `addSelect` — SQL có JOIN nhưng vẫn N+1 khi truy cập
   dữ liệu join (đã giải thích trên).
2. Dùng `innerJoin` khi Post có thể không có Comment nào — Post đó biến mất
   khỏi kết quả (mất dữ liệu ngầm, không phải lỗi rõ ràng).
3. Fetch join với `OneToMany` mà thêm cả `setMaxResults()`/`setFirstResult()`
   (LIMIT/OFFSET) — Doctrine cảnh báo/lỗi vì LIMIT trên kết quả đã JOIN nhân
   bản row sẽ sai số lượng Post thực tế.
4. Tưởng `EXTRA_LAZY` (bài 08) giải quyết được N+1 — nó chỉ tối ưu
   `count()`/`contains()` cho MỘT collection, không tối ưu việc load nhiều
   Post cùng lúc.
5. Không đo bằng SQL logging thật, chỉ "tin" là đã tối ưu — luôn xác minh số
   query thật như cách làm ở trên.

## Bài tập

- **Cơ bản**: implement `PostRepository::findAllWithCommentsFetchJoin()`
  dùng `createQuery()->getQueryBuilder()` + `addSelect` + `leftJoin`.
- **Nâng cao**: viết `PostRepository::findTop5ByCommentCount(): array` trả
  về 5 Post có nhiều Comment nhất, dùng DQL với `COUNT()` + `GROUP BY` +
  `ORDER BY ... DESC` + `setMaxResults(5)` — KHÔNG load toàn bộ Comment vào
  PHP để đếm bằng `count()` (đó sẽ là chính N+1 bạn vừa học cách tránh).
- **Thử thách**: gắn `DebugStack` (như ví dụ trên) vào chính bài tập của
  bạn ở bài 06 hoặc 07, đo số query thật khi lấy danh sách `Contributor`
  kèm role trên từng `Post`, hoặc khi lấy toàn bộ cây `Category` — chứng
  minh bằng số liệu, không chỉ đoán.

## Cách tự kiểm tra

```bash
./flow doctrine:validate
./flow doctrine:dql "SELECT p, c FROM MKintai\DoctrineLab\Domain\Model\Lesson10\Post p LEFT JOIN p.comments c"
```

Hoặc viết một CommandController tạm gắn `DebugStack` như ví dụ, in số query
ra — so với con số đã đo thật ở trên (7 vs 1) để biết code của bạn có đúng
hướng không.

## Quiz

1. Vì sao `leftJoin` không đủ để tránh N+1 — phải có thêm gì, và vì sao
   thiếu nó vẫn "trông như" đã tối ưu (SQL có JOIN) nhưng thực chất không?
2. Vì sao dùng `count()` trên `$post->getComments()` sau khi đã load bằng
   `findAllWithCommentsFetchJoin()` KHÔNG sinh thêm query, nhưng cũng gọi đó
   sau `findAll()` (không fetch join) thì lại sinh 1 query mỗi lần?
3. Fetch join với `LIMIT` trên quan hệ OneToMany có vấn đề gì? Đề xuất cách
   viết `findTop5ByCommentCount()` (bài nâng cao) để không rơi vào vấn đề
   đó.

Đáp án: `solutions/10-querying-relations.md`.
