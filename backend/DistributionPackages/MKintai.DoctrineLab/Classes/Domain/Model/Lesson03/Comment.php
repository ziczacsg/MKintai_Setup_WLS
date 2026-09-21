<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson03;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson03_comment')]
class Comment
{
    #[ORM\Column(type: 'text')]
    protected string $body;

    // TODO: declare the owning side of the relation to Post here.
    // Requirements: ManyToOne, inversedBy "comments", NOT NULL, join column name "post".
    protected Post $post;

    public function __construct(string $body, Post $post)
    {
        $this->body = $body;
        $this->post = $post;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): void
    {
        $this->post = $post;
    }
}
