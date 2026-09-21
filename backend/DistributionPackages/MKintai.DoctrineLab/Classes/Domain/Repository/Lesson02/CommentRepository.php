<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Repository\Lesson02;

use MKintai\DoctrineLab\Domain\Model\Lesson02\Comment;
use MKintai\DoctrineLab\Domain\Model\Lesson02\Post;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;

/**
 * @extends Repository<Comment>
 */
#[Flow\Scope('singleton')]
class CommentRepository extends Repository
{
    /**
     * TODO (Nang cao): return all Comments belonging to the given Post,
     * using $this->createQuery() (Flow QueryInterface), not a raw SQL string.
     *
     * @return Comment[]
     */
    public function findByPost(Post $post): array
    {
        throw new \RuntimeException('TODO: implement findByPost()');
    }
}
