<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Repository\Lesson05;

use MKintai\DoctrineLab\Domain\Model\Lesson05\Tag;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;

/**
 * @extends Repository<Tag>
 */
#[Flow\Scope('singleton')]
class TagRepository extends Repository
{
}
