<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson10;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson10_comment')]
class Comment
{
    #[ORM\Column(type: 'text')]
    protected string $body;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(name: 'post', referencedColumnName: 'persistence_object_identifier', nullable: false)]
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
