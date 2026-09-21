<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Repository\Lesson07;

use MKintai\DoctrineLab\Domain\Model\Lesson07\Category;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;

/**
 * @extends Repository<Category>
 */
#[Flow\Scope('singleton')]
class CategoryRepository extends Repository
{
}
