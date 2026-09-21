<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson08;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson08_invoice_line')]
class InvoiceLine
{
    #[ORM\Column(type: 'string', length: 120)]
    protected string $description;

    // TODO: declare ManyToOne to Invoice here (inversedBy "lines", join column "invoice").
    // ALSO set a DATABASE-level onDelete rule on the JoinColumn (look up the
    // 'onDelete' option). This is independent from ORM-level cascade/orphanRemoval:
    // it must still protect the row even if someone deletes an Invoice via raw SQL,
    // bypassing Doctrine entirely.
    protected Invoice $invoice;

    public function __construct(string $description, Invoice $invoice)
    {
        $this->description = $description;
        $this->invoice = $invoice;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }
}
