<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.lib.content_object.rating_question
 */

/**
 * This class represents an open question
 */
class AssessmentRatingQuestion extends ContentObject implements Versionable
{
    const PROPERTY_CORRECT = 'correct';
    const PROPERTY_FEEDBACK = 'feedback';
    const PROPERTY_HIGH = 'high';
    const PROPERTY_HINT = 'hint';
    const PROPERTY_LOW = 'low';

    public static function get_additional_property_names()
    {
        return array(
            self::PROPERTY_LOW,
            self::PROPERTY_HIGH,
            self::PROPERTY_CORRECT,
            self::PROPERTY_FEEDBACK,
            self::PROPERTY_HINT
        );
    }

    public function get_correct()
    {
        return $this->get_additional_property(self::PROPERTY_CORRECT);
    }

    public function get_feedback()
    {
        return $this->get_additional_property(self::PROPERTY_FEEDBACK);
    }

    public function get_high()
    {
        return $this->get_additional_property(self::PROPERTY_HIGH);
    }

    public function get_hint()
    {
        return $this->get_additional_property(self::PROPERTY_HINT);
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

    public function get_low()
    {
        return $this->get_additional_property(self::PROPERTY_LOW);
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'repository_assessment_rating_question';
    }

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class, true);
    }

    public function has_hint()
    {
        return StringUtilities::getInstance()->hasValue($this->get_hint(), true);
    }

    public function set_correct($value)
    {
        $this->set_additional_property(self::PROPERTY_CORRECT, $value);
    }

    public function set_feedback($feedback)
    {
        $this->set_additional_property(self::PROPERTY_FEEDBACK, $feedback);
    }

    public function set_high($value)
    {
        $this->set_additional_property(self::PROPERTY_HIGH, $value);
    }

    public function set_hint($hint)
    {
        return $this->set_additional_property(self::PROPERTY_HINT, $hint);
    }

    public function set_low($value)
    {
        $this->set_additional_property(self::PROPERTY_LOW, $value);
    }
}
