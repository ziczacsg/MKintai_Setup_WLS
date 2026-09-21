<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson10;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson10_post')]
class Post
{
    #[ORM\Column(type: 'string', length: 120)]
    protected string $title;

    /**
     * @var Collection<Comment>
     */
    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class)]
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

    public function addComment(Comment $comment): void
    {
        $this->comments->add($comment);
        $comment->setPost($this);
    }
}
