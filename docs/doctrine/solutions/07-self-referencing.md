# Đáp án 07 — Self-referencing

## Cơ bản: mapping Category

```php
#[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'children')]
#[ORM\JoinColumn(name: 'parent', referencedColumnName: 'persistence_object_identifier', nullable: true)]
protected ?Category $parent = null;

#[ORM\OneToMany(mappedBy: 'parent', targetEntity: Category::class, cascade: ['persist'])]
protected Collection $children;
```

`cascade: ['persist']` là BẮT BUỘC ở đây (khác `Comment` bài 03) vì
`Category` có `CategoryRepository` → là aggregate root → Flow không tự
cascade. Đã kiểm chứng thật:

```
lesson07_category row count after persisting only root: 3
```

## Nâng cao: mapping + hành vi User follow

```php
#[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'followers')]
#[ORM\JoinTable(
    name: 'lesson07_user_follows',
    joinColumns: [new ORM\JoinColumn(name: 'follower', referencedColumnName: 'persistence_object_identifier')],
    inverseJoinColumns: [new ORM\JoinColumn(name: 'followee', referencedColumnName: 'persistence_object_identifier')]
)]
protected Collection $following;

#[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'following')]
protected Collection $followers;

public function follow(User $user): void
{
    if ($user === $this || $this->following->contains($user)) {
        return;
    }
    $this->following->add($user);
    $user->followers->add($this);
}

public function unfollow(User $user): void
{
    $this->following->removeElement($user);
    $user->followers->removeElement($this);
}
```

## Thử thách

`Category::getDepth()`:
```php
public function getDepth(): int
{
    $depth = 0;
    $node = $this->parent;
    while ($node !== null) {
        $depth++;
        $node = $node->getParent();
    }
    return $depth;
}
```

Với cây 1000 cấp, gọi cho 1 Category tốn tới 1000 query riêng biệt (mỗi lần
`getParent()` trên một Category chưa load là 1 lazy-load) nếu chưa từng
load các cha trong request — N+1 theo độ sâu. Hai cách khắc phục thường
dùng: (a) fetch join nhiều cấp bằng DQL lồng `LEFT JOIN` (chỉ thực tế cho
độ sâu cố định, biết trước); (b) lưu thêm cột `path` (materialized path,
ví dụ `/1/4/9/`) hoặc `depth` ngay trên entity, cập nhật khi thêm/di chuyển
node — đọc 1 query duy nhất, đánh đổi phải tự đồng bộ cột đó khi cây thay
đổi.

## Quiz

1. Vì cả 2 phía của quan hệ trỏ CÙNG một class (`User`), tên cột tự sinh
   (`buildJoinTableColumnName`) sẽ TRÙNG NHAU cho cả joinColumn và
   inverseJoinColumn nếu không chỉ định tường minh — dẫn tới join table chỉ
   còn 1 cột (đã thấy lỗi này thật). ManyToMany bình thường (Post/Tag) có 2
   class khác nhau nên tên cột tự sinh khác nhau, không xung đột.
2. `Category::$children` cần `cascade: ['persist']` vì `Category` CÓ
   Repository (là aggregate root) — Flow chỉ tự set cascade khi target
   KHÔNG phải aggregate root (`Comment` ở bài 03 không có Repository).
3. Nếu xóa `CategoryRepository`, `Category` không còn là aggregate root →
   Flow sẽ tự áp `cascade=['all']` VÀ ép `orphanRemoval=true` (không thể
   tắt) cho `$children` — ngược hẳn với hiện tại, nơi bạn kiểm soát hoàn
   toàn qua khai báo tường minh.
