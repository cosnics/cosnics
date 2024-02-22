<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;

/**
 * @package Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass
 */
class ComplexLearningPath extends ComplexContentObjectItem
{
    public const CONTEXT = LearningPath::CONTEXT;

    public function get_allowed_types(): array
    {
        return [Section::class];
    }
}
