# Đáp án 03 — OneToMany hai chiều

## Cơ bản: mapping

```php
// Post.php
#[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class)]
protected Collection $comments;

// Comment.php
#[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'comments')]
#[ORM\JoinColumn(name: 'post', referencedColumnName: 'persistence_object_identifier', nullable: false)]
protected Post $post;
```

## Nâng cao: `addComment()` / `removeComment()`

```php
public function addComment(Comment $comment): void
{
    if ($this->comments->contains($comment)) {
        return;
    }
    $this->comments->add($comment);
    $comment->setPost($this);
}

public function removeComment(Comment $comment): void
{
    $this->comments->removeElement($comment);
}
```

## Thử thách — kết quả đã chạy thật

```
lesson03_comment row count after persisting only Post: 1
```

Tạo `Post`, `addComment()`, CHỈ gọi `$postRepository->add($post)` +
`persistAll()` — không chạm `Comment` hay Repository nào của nó — Comment
vẫn insert. Chứng minh cascade tự động của Flow cho entity không có
Repository.

## Quiz

1. Doctrine chỉ đọc owning side vì đó là thiết kế cố tình để tránh mơ hồ:
   nếu đọc cả 2 phía mà chúng mâu thuẫn (ví dụ Comment trỏ Post A nhưng nằm
   trong collection của Post B), Doctrine sẽ không biết tin phía nào. Owning
   side (nơi có cột FK thật) luôn là "nguồn sự thật" duy nhất.
2. Nếu thêm `CommentRepository`, `Comment` trở thành aggregate root
   (`isAggregateRoot() === true`), và theo đúng dòng logic ở
   `FlowAnnotationDriver:614-628`, Flow sẽ KHÔNG còn tự set
   `cascade=['all']`/`orphanRemoval=true` nữa — bạn phải tự khai báo nếu vẫn
   muốn hành vi đó (giống `Invoice`/`InvoiceLine` ở bài 08).
3. Nguy hiểm vì bạn KHÔNG THỂ tắt `orphanRemoval` khi Comment không có
   Repository (đã kiểm chứng ở bài 08: dù khai `orphanRemoval: false`, Flow
   vẫn ép `true`). Nếu ở đâu đó trong hệ thống bạn coi Comment như một entity
   độc lập (ví dụ có API riêng để sửa Comment không qua Post), việc gỡ nó
   khỏi một collection Post (dù vô tình) sẽ XÓA THẬT dữ liệu, không chỉ mất
   liên kết.
