<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Repository\Lesson07;

use MKintai\DoctrineLab\Domain\Model\Lesson07\User;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;

/**
 * @extends Repository<User>
 */
#[Flow\Scope('singleton')]
class UserRepository extends Repository
{
}
