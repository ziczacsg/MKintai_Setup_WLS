<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson08;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson08_order')]
class Order
{
    #[ORM\Column(type: 'string', length: 40)]
    protected string $reference;

    // TODO: declare the inverse OneToMany to OrderItem here (mappedBy "order").
    // Also set fetch mode to EXTRA_LAZY (OrderItem has no Repository of its own,
    // so it is NOT an aggregate root -- Flow will apply cascade=['all'] and
    // orphanRemoval=true automatically here, regardless of what you write).
    /**
     * @var Collection<OrderItem>
     */
    protected Collection $items;

    public function __construct(string $reference)
    {
        $this->reference = $reference;
        $this->items = new ArrayCollection();
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return Collection<OrderItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): void
    {
        $this->items->add($item);
        $item->setOrder($this);
    }

    public function removeItem(OrderItem $item): void
    {
        $this->items->removeElement($item);
    }
}
