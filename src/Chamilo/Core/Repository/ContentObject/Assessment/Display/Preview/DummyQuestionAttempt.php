<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Preview;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Attempt\AbstractQuestionAttempt;
use Chamilo\Libraries\Utilities\UUID;

/**
 *
 * @package core\repository\content_object\assessment\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DummyQuestionAttempt extends AbstractQuestionAttempt
{
    const PROPERTY_ATTEMPT_ID = 'attempt_id';

    /**
     *
     * @param string[] $extendedPropertyNames
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_ATTEMPT_ID;
        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     *
     * @return int
     */
    public function get_attempt_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_ATTEMPT_ID);
    }

    /**
     *
     * @param int $attempt_id
     */
    public function set_attempt_id($attempt_id)
    {
        $this->setDefaultProperty(self::PROPERTY_ATTEMPT_ID, $attempt_id);
    }

    /**
     *
     * @see \libraries\storage\DataClass::update()
     */
    public function update(): bool
    {
        return PreviewStorage::getInstance()->update_assessment_question_attempt($this);
    }

    /**
     *
     * @see \libraries\storage\DataClass::create()
     */
    public function create(): bool
    {
        $this->set_id(UUID::v4());
        return PreviewStorage::getInstance()->create_assessment_question_attempt($this);
    }

    /**
     *
     * @see \libraries\storage\DataClass::delete()
     */
    public function delete(): bool
    {
        return PreviewStorage::getInstance()->delete_assessment_question_attempt($this);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_assessment_preview_question_attempt';
    }
}
