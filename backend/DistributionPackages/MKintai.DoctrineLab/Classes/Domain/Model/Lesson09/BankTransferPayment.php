<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson09;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson09_bank_transfer_payment')]
class BankTransferPayment extends Payment
{
    #[ORM\Column(type: 'string', length: 34)]
    protected string $iban;

    public function __construct(int $amountInCents, string $iban)
    {
        parent::__construct($amountInCents);
        $this->iban = $iban;
    }

    public function getIban(): string
    {
        return $this->iban;
    }
}
