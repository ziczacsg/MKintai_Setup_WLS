# Đáp án 04 — OneToOne

## Cơ bản: mapping

```php
// User.php
#[ORM\OneToOne(mappedBy: 'user', targetEntity: Profile::class)]
protected ?Profile $profile = null;

// Profile.php
#[ORM\OneToOne(targetEntity: User::class, inversedBy: 'profile')]
#[ORM\JoinColumn(name: 'user', referencedColumnName: 'persistence_object_identifier', nullable: false, unique: true)]
protected User $user;
```

## Nâng cao: factory đồng bộ 2 phía

```php
public static function createWithProfile(string $email, string $bio): self
{
    $user = new self($email);
    $profile = new Profile($bio, $user);
    $user->setProfile($profile);
    return $user;
}
```

## Thử thách

Bỏ `unique: true` khỏi `JoinColumn` → DB KHÔNG chặn tạo 2 `Profile` cho
cùng 1 `User` (chỉ còn là FK bình thường, giống ManyToOne). Điều này cho
thấy: bản thân attribute `#[ORM\OneToOne]` chỉ là gợi ý cho DOCTRINE biết
cách bạn MUỐN dùng quan hệ trong PHP (một `Profile` object cho mỗi `User`
object khi bạn tự viết code đúng cách) — nó KHÔNG tự tạo ràng buộc DB. Ràng
buộc thật (ngăn 2 hàng cùng giá trị `user`) chỉ có khi bạn tự thêm
`unique: true`. Nói cách khác: "1-1" ở tầng ORM là một quy ước bạn phải tự
tôn trọng trong code; "1-1" ở tầng DB là một constraint SQL thật sự chặn
được insert sai.

## Quiz

1. `PersistenceMagicAspect` gắn `persistence_object_identifier` cho MỌI
   class được nhận diện là entity (`classAnnotatedWith(Flow\Entity) ||
   classAnnotatedWith(Doctrine\ORM\Mapping\Entity)`) — không có ngoại lệ để
   nói "class này dùng chung PK với class khác". Vì vậy `Profile` luôn có PK
   riêng của nó; không thể ép nó dùng thẳng giá trị PK của `User`.
2. `unique: true` biến một FK bình thường (cho phép N-1) thành ràng buộc
   1-1 thật ở DB — không có nó, tầng DB cho phép nhiều `Profile` trỏ cùng
   một `User`, dù ORM "nghĩ" là 1-1.
3. Chỉ cần xóa property `$profile` + attribute trên `User` — `Profile` giữ
   nguyên (nó vẫn owning side, vẫn biết `User` của nó). Quan hệ vẫn hoạt
   động đầy đủ ở tầng DB và từ phía `Profile`, chỉ mất khả năng gọi
   `$user->getProfile()`.
