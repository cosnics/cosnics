<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.lib.content_object.assessment_open_question
 */

/**
 * This class represents an open question
 */
class AssessmentOpenQuestion extends ContentObject implements Versionable
{
    const PROPERTY_QUESTION_TYPE = 'question_type';
    const PROPERTY_FEEDBACK = 'feedback';
    const PROPERTY_HINT = 'hint';
    const TYPE_OPEN = 1;
    const TYPE_OPEN_WITH_DOCUMENT = 2;
    const TYPE_DOCUMENT = 3;

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_QUESTION_TYPE, self::PROPERTY_FEEDBACK, self::PROPERTY_HINT);
    }

    public function get_question_type()
    {
        return $this->get_additional_property(self::PROPERTY_QUESTION_TYPE);
    }

    public function set_question_type($question_type)
    {
        $this->set_additional_property(self::PROPERTY_QUESTION_TYPE, $question_type);
    }

    public function get_feedback()
    {
        return $this->get_additional_property(self::PROPERTY_FEEDBACK);
    }

    public function set_feedback($feedback)
    {
        $this->set_additional_property(self::PROPERTY_FEEDBACK, $feedback);
    }

    public function set_hint($hint)
    {
        return $this->set_additional_property(self::PROPERTY_HINT, $hint);
    }

    public function get_hint()
    {
        return $this->get_additional_property(self::PROPERTY_HINT);
    }

    public function has_hint()
    {
        return StringUtilities::getInstance()->hasValue($this->get_hint(), true);
    }

    public function get_types()
    {
        $types = array();
        $types[self::TYPE_OPEN] = Translation::get('OpenQuestion');
        $types[self::TYPE_OPEN_WITH_DOCUMENT] = Translation::get('OpenQuestionWithDocument');
        $types[self::TYPE_DOCUMENT] = Translation::get('DocumentQuestion');
        return $types;
    }

    // TODO: should be moved to an additional parent layer "question" which offers a default implementation.
    public function get_default_weight()
    {
        return 1;
    }

    /**
     * Returns the names of the properties which are UI-wise filled by the integrated html editor
     *
     * @return multitype:string
     */
    public static function get_html_editors($html_editors = array())
    {
        return parent::get_html_editors(array(self::PROPERTY_HINT, self::PROPERTY_FEEDBACK));
    }
}
