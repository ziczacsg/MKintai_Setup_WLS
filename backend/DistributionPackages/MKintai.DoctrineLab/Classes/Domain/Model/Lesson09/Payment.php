<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson09;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson09_payment')]
// TODO: make this Joined Table Inheritance. Add:
// - #[ORM\InheritanceType('JOINED')]
// - #[ORM\DiscriminatorColumn(name: 'type', type: 'string', length: 20)]
// - #[ORM\DiscriminatorMap([...]) mapping a short string to each subclass
abstract class Payment
{
    #[ORM\Column(type: 'integer')]
    protected int $amountInCents;

    public function __construct(int $amountInCents)
    {
        $this->amountInCents = $amountInCents;
    }

    public function getAmountInCents(): int
    {
        return $this->amountInCents;
    }
}
