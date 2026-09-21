<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson04;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson04_user')]
class User
{
    #[ORM\Column(type: 'string', length: 120, unique: true)]
    protected string $email;

    // TODO: declare the inverse side of the OneToOne relation to Profile here.
    // Requirements: OneToOne, mappedBy "user".
    protected ?Profile $profile = null;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): void
    {
        $this->profile = $profile;
    }
}
