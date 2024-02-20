<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassVirtualExtensionInterface;

/**
 * @package repository.lib.content_object.learning_path
 */
class ComplexLearningPath extends ComplexContentObjectItem implements DataClassVirtualExtensionInterface
{
    public const CONTEXT = LearningPath::CONTEXT;

    public function get_allowed_types(): array
    {
        return [Section::class];
    }
}
