<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson09;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson09_customer')]
class Customer
{
    #[ORM\Column(type: 'string', length: 120)]
    protected string $name;

    /**
     * No ORM attribute needed: Address is #[Flow\ValueObject] (embedded=true
     * by default), so Flow embeds its columns as address_street, address_zip_code,
     * address_city directly into lesson09_customer.
     */
    protected Address $address;

    public function __construct(string $name, Address $address)
    {
        $this->name = $name;
        $this->address = $address;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }
}
