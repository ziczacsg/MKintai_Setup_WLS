<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Repository\Lesson10;

use MKintai\DoctrineLab\Domain\Model\Lesson10\Post;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;

/**
 * @extends Repository<Post>
 */
#[Flow\Scope('singleton')]
class PostRepository extends Repository
{
    /**
     * TODO: load every Post together with its Comments in a SINGLE SQL query
     * (fetch join), instead of the N lazy-loading queries you'd get from just
     * calling findAll() and then $post->getComments() on each result.
     * Hint: $this->createQuery()->getQueryBuilder() gives you the underlying
     * Doctrine QueryBuilder (default root alias is "e"); use addSelect() + leftJoin().
     * Verify with SQL logging (see lesson doc) that this produces exactly 1 query.
     *
     * @return Post[]
     */
    public function findAllWithCommentsFetchJoin(): array
    {
        throw new \RuntimeException('TODO: implement findAllWithCommentsFetchJoin()');
    }
}
