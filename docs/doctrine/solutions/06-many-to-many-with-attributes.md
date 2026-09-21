# Đáp án 06 — ManyToMany có thuộc tính phụ

## Cơ bản: mapping

```php
// PostContributor.php
#[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'contributorAssignments')]
#[ORM\JoinColumn(name: 'post', referencedColumnName: 'persistence_object_identifier', nullable: false)]
protected Post $post;

#[ORM\ManyToOne(targetEntity: Contributor::class, inversedBy: 'postAssignments')]
#[ORM\JoinColumn(name: 'contributor', referencedColumnName: 'persistence_object_identifier', nullable: false)]
protected Contributor $contributor;

// Post.php
#[ORM\OneToMany(mappedBy: 'post', targetEntity: PostContributor::class)]
protected Collection $contributorAssignments;

// Contributor.php
#[ORM\OneToMany(mappedBy: 'contributor', targetEntity: PostContributor::class)]
protected Collection $postAssignments;
```

## Nâng cao: `Post::addContributor()`

```php
public function addContributor(Contributor $contributor, string $role): PostContributor
{
    foreach ($this->contributorAssignments as $assignment) {
        if ($assignment->getContributor() === $contributor && $assignment->getRole() === $role) {
            return $assignment;
        }
    }
    $assignment = new PostContributor($this, $contributor, $role);
    $this->contributorAssignments->add($assignment);
    return $assignment;
}
```

## Thử thách: `getRoleOnPost()`

```php
// PHP thuần, không DQL
public function getRoleOnPost(Post $post): ?string
{
    foreach ($this->postAssignments as $assignment) {
        if ($assignment->getPost() === $post) {
            return $assignment->getRole();
        }
    }
    return null;
}
```

Nếu gọi cho N Contributor mà mỗi lần `getPostAssignments()` chưa được load
trong request, đây chính là N+1 (mỗi Contributor lazy-load riêng
`postAssignments`). Bản DQL tương đương (bài 10 dạy kỹ):

```php
$query = $this->createQuery()->getQueryBuilder();
$query->select('pc.role')
    ->from(PostContributor::class, 'pc')
    ->where('pc.contributor = :contributor AND pc.post = :post')
    ->setParameter('contributor', $contributor)
    ->setParameter('post', $post);
```

## Quiz

1. Doctrine không cho cột phụ trên `#[ORM\ManyToMany]` vì join table thuần
   chỉ có 2 cột FK — không có "chỗ" khái niệm cho cột phụ trong metadata
   ManyToMany (bản chất nó chỉ định nghĩa QUAN HỆ, không định nghĩa một
   entity có thuộc tính riêng). Muốn có thuộc tính, quan hệ đó không còn là
   "association" đơn giản nữa mà là một entity thật.
2. `PostContributor` có PK riêng (`persistence_object_identifier`) tách biệt
   hoàn toàn khỏi (post, contributor) — nghĩa là kỹ thuật có thể có 2 dòng
   với CÙNG (post, contributor) nếu bạn không tự chặn (như `role` khác nhau
   ở bài nâng cao). Join table thuần ở bài 05 dùng PK KÉP chính là
   (post, tag) → DB tự chặn trùng lặp hoàn toàn, không cần code tự kiểm tra.
3. Không hỗ trợ trực tiếp — cần bỏ unique theo (post, contributor) nếu có,
   và đảm bảo `addContributor()` cho phép nhiều assignment cùng post+contributor
   miễn role khác nhau (đã làm ở bài Nâng cao trên, kiểm tra cả role trong
   điều kiện idempotent).
