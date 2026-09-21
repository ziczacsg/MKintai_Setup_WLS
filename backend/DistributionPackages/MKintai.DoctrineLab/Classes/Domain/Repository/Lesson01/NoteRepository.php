<?php
declare(strict_types=1);

namespace MKintai\DoctrineLab\Domain\Repository\Lesson01;

use MKintai\DoctrineLab\Domain\Model\Lesson01\Note;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;

/**
 * @extends Repository<Note>
 */
#[Flow\Scope('singleton')]
class NoteRepository extends Repository
{
}
