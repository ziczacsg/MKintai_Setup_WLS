<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson02;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson02_comment')]
class Comment
{
    #[ORM\Column(type: 'text')]
    protected string $body;

    // TODO: declare the relation to Post here.
    // Requirements: ManyToOne, owning side, NOT NULL, join column name "post".
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
}
