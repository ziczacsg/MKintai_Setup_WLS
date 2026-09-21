<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson03;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson03_post')]
class Post
{
    #[ORM\Column(type: 'string', length: 120)]
    protected string $title;

    // TODO: declare the inverse side of the relation to Comment here.
    // Requirements: OneToMany, mappedBy "post".
    /**
     * @var Collection<Comment>
     */
    protected Collection $comments;

    public function __construct(string $title)
    {
        $this->title = $title;
        $this->comments = new ArrayCollection();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return Collection<Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    // TODO (Nang cao): implement addComment() so BOTH sides of the relation
    // stay in sync: add to $this->comments AND call $comment->setPost($this).
    // Must be idempotent (adding the same Comment twice must not duplicate it).
    public function addComment(Comment $comment): void
    {
        throw new \RuntimeException('TODO: implement addComment()');
    }

    // TODO (Nang cao): implement removeComment() -- remove from the collection.
    public function removeComment(Comment $comment): void
    {
        throw new \RuntimeException('TODO: implement removeComment()');
    }
}
