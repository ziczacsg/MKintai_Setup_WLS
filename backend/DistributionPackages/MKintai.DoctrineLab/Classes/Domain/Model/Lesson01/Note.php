<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson01;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson01_note')]
class Note
{
    #[ORM\Column(type: 'string', length: 120)]
    protected string $title;

    #[ORM\Column(type: 'text')]
    protected string $body;

    public function __construct(string $title, string $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
