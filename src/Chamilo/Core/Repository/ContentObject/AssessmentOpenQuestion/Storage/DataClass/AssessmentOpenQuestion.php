<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\VersionableInterface;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Storage\DataClass
 */
class AssessmentOpenQuestion extends ContentObject implements VersionableInterface
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion';

    public const PROPERTY_FEEDBACK = 'feedback';
    public const PROPERTY_HINT = 'hint';
    public const PROPERTY_QUESTION_TYPE = 'question_type';

    public const TYPE_DOCUMENT = 3;
    public const TYPE_OPEN = 1;
    public const TYPE_OPEN_WITH_DOCUMENT = 2;

    public static function getAdditionalPropertyNames(): array
    {
        return [self::PROPERTY_QUESTION_TYPE, self::PROPERTY_FEEDBACK, self::PROPERTY_HINT];
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_assessment_open_question';
    }

    public function get_default_weight()
    {
        return 1;
    }

    public function get_feedback()
    {
        return $this->getAdditionalProperty(self::PROPERTY_FEEDBACK);
    }

    public function get_hint()
    {
        return $this->getAdditionalProperty(self::PROPERTY_HINT);
    }

    /**
     * Returns the names of the properties which are UI-wise filled by the integrated html editor
     *
     * @return string[]
     */
    public static function get_html_editors($html_editors = [])
    {
        return parent::get_html_editors([self::PROPERTY_HINT, self::PROPERTY_FEEDBACK]);
    }

    public function get_question_type()
    {
        return $this->getAdditionalProperty(self::PROPERTY_QUESTION_TYPE);
    }

    public static function get_types()
    {
        $types = [];
        $types[self::TYPE_OPEN] = Translation::get('OpenQuestion');
        $types[self::TYPE_OPEN_WITH_DOCUMENT] = Translation::get('OpenQuestionWithDocument');
        $types[self::TYPE_DOCUMENT] = Translation::get('DocumentQuestion');

        return $types;
    }

    public function has_hint()
    {
        return StringUtilities::getInstance()->hasValue($this->get_hint(), true);
    }

    public function set_feedback($feedback)
    {
        $this->setAdditionalProperty(self::PROPERTY_FEEDBACK, $feedback);
    }

    // TODO: should be moved to an additional parent layer "question" which offers a default implementation.

    public function set_hint($hint)
    {
        return $this->setAdditionalProperty(self::PROPERTY_HINT, $hint);
    }

    public function set_question_type($question_type)
    {
        $this->setAdditionalProperty(self::PROPERTY_QUESTION_TYPE, $question_type);
    }
}
