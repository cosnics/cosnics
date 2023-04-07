<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;

/**
 *
 * @package repository.lib.content_object.learning_path
 */
class ComplexLearningPath extends ComplexContentObjectItem
{

    public function get_allowed_types(): array
    {
        return array(Section::class);
    }
}
