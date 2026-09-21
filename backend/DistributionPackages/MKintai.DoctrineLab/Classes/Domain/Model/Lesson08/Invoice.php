<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson08;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson08_invoice')]
class Invoice
{
    #[ORM\Column(type: 'string', length: 40)]
    protected string $reference;

    // TODO: declare the inverse OneToMany to InvoiceLine here (mappedBy "invoice").
    // InvoiceLine HAS its own Repository (see InvoiceLineRepository) -- it IS an
    // aggregate root, unlike OrderItem in Order above. That means whatever cascade
    // and orphanRemoval you declare here will be honoured as-is by Flow (no auto
    // override). Decide: do you want persisting the Invoice to also persist new
    // InvoiceLines? Do you want removing a line from this collection to delete its
    // row, or just detach it? Justify your choice in the quiz.
    /**
     * @var Collection<InvoiceLine>
     */
    protected Collection $lines;

    public function __construct(string $reference)
    {
        $this->reference = $reference;
        $this->lines = new ArrayCollection();
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return Collection<InvoiceLine>
     */
    public function getLines(): Collection
    {
        return $this->lines;
    }

    public function addLine(InvoiceLine $line): void
    {
        $this->lines->add($line);
    }

    public function removeLine(InvoiceLine $line): void
    {
        $this->lines->removeElement($line);
    }
}
