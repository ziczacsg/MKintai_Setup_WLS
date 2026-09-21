<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson09;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
class SmsNotification extends Notification
{
    #[ORM\Column(type: 'string', length: 20)]
    protected string $phoneNumber;

    public function __construct(string $message, string $phoneNumber)
    {
        parent::__construct($message);
        $this->phoneNumber = $phoneNumber;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }
}
