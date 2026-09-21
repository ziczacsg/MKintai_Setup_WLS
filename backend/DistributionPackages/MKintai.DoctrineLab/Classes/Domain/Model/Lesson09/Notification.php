<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson09;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson09_notification')]
// TODO: make this Single Table Inheritance. Add:
// - #[ORM\InheritanceType('SINGLE_TABLE')]
// - #[ORM\DiscriminatorColumn(name: 'type', type: 'string', length: 20)]
// - #[ORM\DiscriminatorMap([...]) mapping a short string to each subclass
abstract class Notification
{
    #[ORM\Column(type: 'string', length: 255)]
    protected string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
