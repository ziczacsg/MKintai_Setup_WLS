<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson07;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson07_category')]
class Category
{
    #[ORM\Column(type: 'string', length: 120)]
    protected string $name;

    // TODO: declare the self-referencing ManyToOne to the parent Category here.
    // Requirements: inversedBy "children", join column "parent", NULLABLE (root has no parent).
    protected ?Category $parent = null;

    // TODO: declare the inverse OneToMany to child Categories here (mappedBy "parent").
    // Challenge: Category has its own Repository (it IS an aggregate root), so Flow
    // will NOT auto-cascade here like it did for Comment in lesson 3. Decide for
    // yourself whether persisting a new child needs an explicit cascade option,
    // and verify by persisting only the root Category and checking the DB (see docs/doctrine/07).
    /**
     * @var Collection<Category>
     */
    protected Collection $children;

    public function __construct(string $name, ?Category $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
        $this->children = new ArrayCollection();
        $parent?->addChild($this);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParent(): ?Category
    {
        return $this->parent;
    }

    /**
     * @return Collection<Category>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Category $child): void
    {
        if ($this->children->contains($child)) {
            return;
        }
        $this->children->add($child);
    }
}
