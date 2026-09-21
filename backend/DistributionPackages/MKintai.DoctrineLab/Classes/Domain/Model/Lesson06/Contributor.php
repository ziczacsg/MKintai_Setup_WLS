<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson06;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson06_contributor')]
class Contributor
{
    #[ORM\Column(type: 'string', length: 120)]
    protected string $name;

    // TODO: declare the inverse side of the relation to PostContributor here.
    // Requirements: OneToMany, mappedBy "contributor".
    /**
     * @var Collection<PostContributor>
     */
    protected Collection $postAssignments;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->postAssignments = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<PostContributor>
     */
    public function getPostAssignments(): Collection
    {
        return $this->postAssignments;
    }
}
