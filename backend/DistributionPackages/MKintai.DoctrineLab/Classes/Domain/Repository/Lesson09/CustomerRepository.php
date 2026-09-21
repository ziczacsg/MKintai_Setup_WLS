<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Repository\Lesson09;

use MKintai\DoctrineLab\Domain\Model\Lesson09\Customer;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;

/**
 * @extends Repository<Customer>
 */
#[Flow\Scope('singleton')]
class CustomerRepository extends Repository
{
}
