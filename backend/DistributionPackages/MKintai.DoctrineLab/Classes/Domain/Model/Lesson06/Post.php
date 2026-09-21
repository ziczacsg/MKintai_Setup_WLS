<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson06;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson06_post')]
class Post
{
    #[ORM\Column(type: 'string', length: 120)]
    protected string $title;

    // TODO: declare the inverse side of the relation to PostContributor here.
    // Requirements: OneToMany, mappedBy "post".
    /**
     * @var Collection<PostContributor>
     */
    protected Collection $contributorAssignments;

    public function __construct(string $title)
    {
        $this->title = $title;
        $this->contributorAssignments = new ArrayCollection();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return Collection<PostContributor>
     */
    public function getContributorAssignments(): Collection
    {
        return $this->contributorAssignments;
    }

    // TODO (Nang cao): create a PostContributor, add it to $contributorAssignments,
    // and return it. Must be idempotent for the same (contributor, role) pair.
    public function addContributor(Contributor $contributor, string $role): PostContributor
    {
        throw new \RuntimeException('TODO: implement addContributor()');
    }
}
