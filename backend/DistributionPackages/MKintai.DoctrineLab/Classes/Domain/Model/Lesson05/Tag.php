<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson05;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson05_tag')]
class Tag
{
    #[ORM\Column(type: 'string', length: 60, unique: true)]
    protected string $name;

    // TODO: declare the inverse side of the ManyToMany relation to Post here.
    // Requirements: ManyToMany, mappedBy "tags".
    /**
     * @var Collection<Post>
     */
    protected Collection $posts;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->posts = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): void
    {
        if ($this->posts->contains($post)) {
            return;
        }
        $this->posts->add($post);
    }

    public function removePost(Post $post): void
    {
        $this->posts->removeElement($post);
    }
}
