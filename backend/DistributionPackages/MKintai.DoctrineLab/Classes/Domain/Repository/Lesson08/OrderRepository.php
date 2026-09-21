<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Repository\Lesson08;

use MKintai\DoctrineLab\Domain\Model\Lesson08\Order;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;

/**
 * @extends Repository<Order>
 */
#[Flow\Scope('singleton')]
class OrderRepository extends Repository
{
}
