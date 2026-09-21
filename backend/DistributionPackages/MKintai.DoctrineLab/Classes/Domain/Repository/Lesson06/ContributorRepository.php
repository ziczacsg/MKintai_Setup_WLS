<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Repository\Lesson06;

use MKintai\DoctrineLab\Domain\Model\Lesson06\Contributor;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;

/**
 * @extends Repository<Contributor>
 */
#[Flow\Scope('singleton')]
class ContributorRepository extends Repository
{
}
