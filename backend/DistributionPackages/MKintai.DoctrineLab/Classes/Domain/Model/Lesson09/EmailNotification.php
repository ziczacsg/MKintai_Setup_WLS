<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson09;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
class EmailNotification extends Notification
{
    #[ORM\Column(type: 'string', length: 255)]
    protected string $emailAddress;

    public function __construct(string $message, string $emailAddress)
    {
        parent::__construct($message);
        $this->emailAddress = $emailAddress;
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }
}
