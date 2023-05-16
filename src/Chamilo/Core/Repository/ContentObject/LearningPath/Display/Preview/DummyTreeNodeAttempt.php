<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;

/**
 * @package core\repository\content_object\learning_path\display
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class DummyTreeNodeAttempt extends TreeNodeAttempt
{
    public const CONTEXT = Manager::CONTEXT;

    /**
     * @see \libraries\storage\DataClass::create()
     */
    public function create(): bool
    {
        return true;
    }

    /**
     * @see \libraries\storage\DataClass::delete()
     */
    public function delete(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_learning_path_preview_tree_node_attempt';
    }

    /**
     * @see \libraries\storage\DataClass::update()
     */
    public function update(): bool
    {
        return true;
    }
}
