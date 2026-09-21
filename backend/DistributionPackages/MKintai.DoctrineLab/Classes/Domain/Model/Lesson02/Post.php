<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson02;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson02_post')]
class Post
{
    #[ORM\Column(type: 'string', length: 120)]
    protected string $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
