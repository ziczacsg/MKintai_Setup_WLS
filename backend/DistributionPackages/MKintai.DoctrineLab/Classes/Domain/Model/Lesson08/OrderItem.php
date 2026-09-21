<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson08;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson08_order_item')]
class OrderItem
{
    #[ORM\Column(type: 'string', length: 120)]
    protected string $productName;

    // TODO: declare ManyToOne to Order here (inversedBy "items", join column "order_ref").
    // Note: the column can't be named "order" -- that's a reserved SQL keyword.
    protected Order $order;

    public function __construct(string $productName, Order $order)
    {
        $this->productName = $productName;
        $this->order = $order;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }
}
