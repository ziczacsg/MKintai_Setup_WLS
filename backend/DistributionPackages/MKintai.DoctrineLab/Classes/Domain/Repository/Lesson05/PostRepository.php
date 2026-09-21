<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Repository\Lesson05;

use MKintai\DoctrineLab\Domain\Model\Lesson05\Post;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;

/**
 * @extends Repository<Post>
 */
#[Flow\Scope('singleton')]
class PostRepository extends Repository
{
}
