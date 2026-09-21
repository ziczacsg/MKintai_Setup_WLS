<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Repository\Lesson08;

use MKintai\DoctrineLab\Domain\Model\Lesson08\InvoiceLine;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;

/**
 * @extends Repository<InvoiceLine>
 */
#[Flow\Scope('singleton')]
class InvoiceLineRepository extends Repository
{
}
