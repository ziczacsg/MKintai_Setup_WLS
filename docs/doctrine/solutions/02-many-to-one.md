# Đáp án 02 — ManyToOne một chiều

## Cơ bản: mapping

```php
// Classes/Domain/Model/Lesson02/Comment.php
#[ORM\ManyToOne(targetEntity: Post::class)]
#[ORM\JoinColumn(name: 'post', referencedColumnName: 'persistence_object_identifier', nullable: false)]
protected Post $post;
```

`Post.php` không cần sửa gì (không có phía nào trỏ ngược lại trong quan hệ
một chiều).

## Nâng cao: `CommentRepository::findByPost()`

Đã chạy thật (tạo 1 Post + 2 Comment, gọi `findByPost($post)`, kết quả trả
đúng 2 Comment):

```php
public function findByPost(Post $post): array
{
    $query = $this->createQuery();
    $query->matching($query->equals('post', $post));
    return $query->execute()->toArray();
}
```

Ghi chú: `equals('post', $post)` — truyền cả OBJECT `Post`, không phải
`$post->getPersistenceObjectIdentifier()`. Flow's Query tự resolve object
thành điều kiện so khớp đúng cột FK.

## Thử thách

Xóa `Post` còn `Comment` trỏ tới nó (qua `$postRepository->remove($post)` +
`persistAll()`) sẽ throw lỗi FK constraint từ MySQL
(`Cannot delete or update a parent row: a foreign key constraint fails`),
vì `JoinColumn` không có `onDelete`. Đây là hành vi ĐÚNG cho quan hệ một
chiều "lỏng" như Comment→Post ở bài này: hệ thống buộc bạn phải xử lý rõ
ràng (xóa Comment trước, hoặc cấm xóa Post còn Comment) thay vì để dữ liệu
mồ côi âm thầm. Khác với bài 08, nơi `InvoiceLine`→`Invoice` chủ động dùng
`onDelete: 'CASCADE'` vì nghiệp vụ ở đó CHỦ ĐỊNH muốn xóa theo.

## Quiz

1. Thiết kế một chiều hợp lý khi bên "một" (Post) không cần biết/không nên
   biết về bên "nhiều" (Comment) để giữ module gọn — ví dụ một hệ thống log
   audit trỏ tới User nhưng User không cần list ngược audit log của mình.
   KHÔNG hợp lý khi nghiệp vụ thường xuyên cần duyệt từ Post sang Comment
   (khi đó nên hai chiều như bài 03, tránh phải tự viết query lặp lại).
2. FK luôn nằm ở bảng "nhiều" (Comment) vì mỗi Comment chỉ trỏ tới ĐÚNG MỘT
   Post (một giá trị FK), còn một Post có thể được N Comment trỏ tới — SQL
   không thể biểu diễn "1 cột trỏ tới N dòng" ở phía Post.
3. Comment cần Repository riêng ở bài này vì nó là **aggregate root độc
   lập** — không có gì tự động cascade persist/remove nó qua Post (Post
   không hề biết Comment tồn tại ở thiết kế một chiều này). Nếu xóa
   `CommentRepository` mà giữ nguyên mapping, `Comment` sẽ TRỞ THÀNH
   non-aggregate-root, và bất kỳ chỗ nào trong code lỡ tạo Comment "trôi
   nổi" (không gắn qua collection nào) sẽ không có cách hợp lệ để persist nó
   (Flow ném lỗi vì entity không aggregate root cần được cascade từ đâu đó).
