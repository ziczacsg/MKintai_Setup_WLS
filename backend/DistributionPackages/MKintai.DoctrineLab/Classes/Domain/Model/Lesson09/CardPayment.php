<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson09;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson09_card_payment')]
class CardPayment extends Payment
{
    #[ORM\Column(type: 'string', length: 4)]
    protected string $lastFourDigits;

    public function __construct(int $amountInCents, string $lastFourDigits)
    {
        parent::__construct($amountInCents);
        $this->lastFourDigits = $lastFourDigits;
    }

    public function getLastFourDigits(): string
    {
        return $this->lastFourDigits;
    }
}
