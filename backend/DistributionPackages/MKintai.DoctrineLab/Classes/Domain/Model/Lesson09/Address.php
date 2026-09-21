<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson09;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

// TODO: mark this as a Flow Value Object so Customer::$address gets embedded
// as plain columns (address_street, address_zip_code, address_city) instead of
// erroring out. No ORM\Embedded attribute needed once you do this correctly.
class Address
{
    #[ORM\Column(type: 'string', length: 255)]
    protected string $street;

    #[ORM\Column(type: 'string', length: 20)]
    protected string $zipCode;

    #[ORM\Column(type: 'string', length: 120)]
    protected string $city;

    public function __construct(string $street, string $zipCode, string $city)
    {
        $this->street = $street;
        $this->zipCode = $zipCode;
        $this->city = $city;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }
}
