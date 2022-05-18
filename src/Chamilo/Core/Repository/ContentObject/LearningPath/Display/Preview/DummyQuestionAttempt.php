<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeQuestionAttempt;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DummyQuestionAttempt extends TreeNodeQuestionAttempt
{

    /**
     *
     * @see \libraries\storage\DataClass::update()
     */
    public function update(): bool
    {
        return true;
    }

    /**
     *
     * @see \libraries\storage\DataClass::create()
     */
    public function create(): bool
    {
        return true;
    }

    /**
     *
     * @see \libraries\storage\DataClass::delete()
     */
    public function delete(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'repository_learning_path_preview_question_attempt';
    }
}
