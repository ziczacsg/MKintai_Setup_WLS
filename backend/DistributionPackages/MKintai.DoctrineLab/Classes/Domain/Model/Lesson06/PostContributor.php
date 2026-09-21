<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Model\Lesson06;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

#[Flow\Entity]
#[ORM\Table(name: 'lesson06_post_contributor')]
class PostContributor
{
    // TODO: declare ManyToOne to Post here (inversedBy "contributorAssignments", join column "post").
    protected Post $post;

    // TODO: declare ManyToOne to Contributor here (inversedBy "postAssignments", join column "contributor").
    protected Contributor $contributor;

    #[ORM\Column(type: 'string', length: 40)]
    protected string $role;

    #[ORM\Column(type: 'datetime')]
    protected \DateTime $joinedAt;

    public function __construct(Post $post, Contributor $contributor, string $role)
    {
        $this->post = $post;
        $this->contributor = $contributor;
        $this->role = $role;
        $this->joinedAt = new \DateTime();
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getContributor(): Contributor
    {
        return $this->contributor;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getJoinedAt(): \DateTime
    {
        return $this->joinedAt;
    }
}
