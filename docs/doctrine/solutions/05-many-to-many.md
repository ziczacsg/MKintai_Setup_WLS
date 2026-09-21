# Đáp án 05 — ManyToMany

## Cơ bản: mapping

```php
// Post.php
#[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'posts')]
#[ORM\JoinTable(name: 'lesson05_post_tags_join')]
protected Collection $tags;

// Tag.php
#[ORM\ManyToMany(targetEntity: Post::class, mappedBy: 'tags')]
protected Collection $posts;
```

## Nâng cao: `addTag()` / `removeTag()`

```php
public function addTag(Tag $tag): void
{
    if ($this->tags->contains($tag)) return;
    $this->tags->add($tag);
    $tag->addPost($this);
}

public function removeTag(Tag $tag): void
{
    if (!$this->tags->contains($tag)) return;
    $this->tags->removeElement($tag);
    $tag->removePost($this);
}
```

(`Tag::addPost()`/`removePost()` chỉ thao tác 1 phía nội bộ, không cần gọi
ngược lại `Post` — tránh vòng lặp vô hạn.)

## Thử thách: `TagRepository::findOrCreateByName()`

```php
public function findOrCreateByName(string $name): Tag
{
    $query = $this->createQuery();
    $query->matching($query->equals('name', $name));
    $existing = $query->execute()->getFirst();
    if ($existing !== null) {
        return $existing;
    }
    $tag = new Tag($name);
    $this->add($tag);
    return $tag;
}
```

## Quiz

1. Bug biểu hiện khi trong CÙNG một request/method, sau khi
   `$post->addTag($tag)`, bạn gọi `$tag->getPosts()->contains($post)` —
   nếu quên `$tag->addPost($post)`, kết quả là `false` dù bạn "vừa" thêm
   quan hệ. Sau khi `persistAll()` và load lại (request mới), DB đã đúng
   nên bug "biến mất" — rất khó tái hiện khi debug qua nhiều request.
2. Vì `Tag` có Repository riêng (`TagRepository` tồn tại) →
   `isAggregateRoot(Tag::class) === true`. Theo logic trong
   `FlowAnnotationDriver`, cascade mặc định `['all']` chỉ áp dụng khi
   `isAggregateRoot() === false`; với `Tag` là aggregate root, nhánh đó
   không chạy, cascade không được set → bạn phải tự
   `$tagRepository->add($newTag)`.
3. Đọc đúng source `FlowAnnotationDriver::inferJoinTableNameFromClassAndPropertyName()`
   (dòng 475-490): tên = `inferTableNameFromClassName($className)` (toàn bộ
   FQCN, `\` → `_`, viết thường) + `_` + tên property + `_join`. Với
   `Post::$tags` (namespace đầy đủ
   `MKintai\DoctrineLab\Domain\Model\Lesson05\Post`), tên sinh ra sẽ là
   `mkintai_doctrinelab_domain_model_lesson05_post_tags_join` — DÀI hơn và
   KHÁC quy tắc đặt tên COLUMN trong join table (`buildJoinTableColumnName()`,
   dòng 495-506, chỉ dùng phần cuối namespace package + tên model, ra
   `doctrinelab_lesson05_post` như đã thấy trong SQL thật ở bài này) — hai
   hàm khác nhau, đừng nhầm hai quy tắc với nhau. Tự generate migration
   thật (bỏ `JoinTable(name:)`) để đối chiếu.
