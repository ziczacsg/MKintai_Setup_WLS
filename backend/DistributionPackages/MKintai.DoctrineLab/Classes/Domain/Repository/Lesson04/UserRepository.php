<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Repository\Lesson04;

use MKintai\DoctrineLab\Domain\Model\Lesson04\User;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;

/**
 * @extends Repository<User>
 */
#[Flow\Scope('singleton')]
class UserRepository extends Repository
{
}
