<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson05;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson05_post')]
class Post
{
    #[ORM\Column(type: 'string', length: 120)]
    protected string $title;

    // TODO: declare the owning side of the ManyToMany relation to Tag here.
    // Requirements: ManyToMany, inversedBy "posts", join table name "lesson05_post_tags_join".
    /**
     * @var Collection<Tag>
     */
    protected Collection $tags;

    public function __construct(string $title)
    {
        $this->title = $title;
        $this->tags = new ArrayCollection();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return Collection<Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    // TODO (Nang cao): keep both sides in sync (add to $this->tags AND call
    // $tag->addPost($this)); must be idempotent.
    public function addTag(Tag $tag): void
    {
        throw new \RuntimeException('TODO: implement addTag()');
    }

    // TODO (Nang cao): keep both sides in sync when removing.
    public function removeTag(Tag $tag): void
    {
        throw new \RuntimeException('TODO: implement removeTag()');
    }
}
