<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson07;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson07_user')]
class User
{
    #[ORM\Column(type: 'string', length: 120, unique: true)]
    protected string $handle;

    // TODO: declare the owning side of a SELF-REFERENCING ManyToMany here.
    // Requirements: targetEntity User::class, inversedBy "followers", join table
    // "lesson07_user_follows" with two DISTINCT join column names, e.g. "follower"
    // and "followee" (both referencing persistence_object_identifier). A plain
    // #[ORM\JoinTable(name: ...)] without explicit column names will NOT work here --
    // find out why by trying it and reading the generated migration SQL.
    /**
     * Users this user follows.
     *
     * @var Collection<User>
     */
    protected Collection $following;

    // TODO: declare the inverse side here (mappedBy "following").
    /**
     * Users that follow this user (inverse side, read-only).
     *
     * @var Collection<User>
     */
    protected Collection $followers;

    public function __construct(string $handle)
    {
        $this->handle = $handle;
        $this->following = new ArrayCollection();
        $this->followers = new ArrayCollection();
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    /**
     * @return Collection<User>
     */
    public function getFollowing(): Collection
    {
        return $this->following;
    }

    /**
     * @return Collection<User>
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    // TODO (Nang cao): keep both sides in sync; a User must never follow itself;
    // must be idempotent.
    public function follow(User $user): void
    {
        throw new \RuntimeException('TODO: implement follow()');
    }

    // TODO (Nang cao): keep both sides in sync when unfollowing.
    public function unfollow(User $user): void
    {
        throw new \RuntimeException('TODO: implement unfollow()');
    }
}
