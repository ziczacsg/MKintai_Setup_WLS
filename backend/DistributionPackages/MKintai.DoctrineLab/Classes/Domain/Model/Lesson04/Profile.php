<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson04;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson04_profile')]
class Profile
{
    #[ORM\Column(type: 'string', length: 255)]
    protected string $bio;

    // TODO: declare the owning side of the OneToOne relation to User here.
    // Requirements: OneToOne, inversedBy "profile", join column name "user",
    // NOT NULL and UNIQUE (a User can have at most one Profile).
    protected User $user;

    public function __construct(string $bio, User $user)
    {
        $this->bio = $bio;
        $this->user = $user;
    }

    public function getBio(): string
    {
        return $this->bio;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
