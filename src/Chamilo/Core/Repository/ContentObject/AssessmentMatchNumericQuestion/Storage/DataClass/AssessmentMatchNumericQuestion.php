<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchNumericQuestion\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.lib.content_object.match_numeric_question
 */
class AssessmentMatchNumericQuestion extends ContentObject implements Versionable
{
    const PROPERTY_HINT = 'hint';
    const PROPERTY_OPTIONS = 'options';
    const PROPERTY_TOLERANCE_TYPE = 'tolerance_type';

    const TOLERANCE_TYPE_ABSOLUTE = 'absolute';
    const TOLERANCE_TYPE_RELATIVE = 'relative';

    public function __construct($defaultProperties = [], $additionalProperties = null)
    {
        parent::__construct($defaultProperties, $additionalProperties);
        if (!isset($additionalProperties[self::PROPERTY_TOLERANCE_TYPE]))
        {
            $this->set_tolerance_type(self::TOLERANCE_TYPE_ABSOLUTE);
        }
    }

    public function add_option($option)
    {
        $options = $this->get_options();
        $options[] = $option;

        return $this->set_additional_property(self::PROPERTY_OPTIONS, serialize($options));
    }

    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_TOLERANCE_TYPE, self::PROPERTY_OPTIONS, self::PROPERTY_HINT);
    }

    public function get_best_option()
    {
        $best_score = 0;
        $best_option = null;

        foreach ($this->get_options() as $key => $option)
        {
            if ($option->get_score() >= $best_score)
            {
                $best_score = $option->get_score();
                $best_option = $option;
            }
        }

        return $best_option;
    }

    public function get_default_weight()
    {
        return $this->get_maximum_score();
    }

    public function get_hint()
    {
        return $this->get_additional_property(self::PROPERTY_HINT);
    }

    /**
     * Returns the names of the properties which are UI-wise filled by the integrated html editor
     *
     * @return string[]
     */
    public static function get_html_editors($html_editors = [])
    {
        return parent::get_html_editors(array(self::PROPERTY_HINT));
    }

    /**
     * Returns the maximum weight/score a user can receive.
     */
    public function get_maximum_score()
    {
        $max = 0;
        $options = $this->get_options();
        foreach ($options as $option)
        {
            $max = max($option->get_score(), $max);
        }

        return $max;
    }

    public function get_number_of_options()
    {
        return count($this->get_options());
    }

    public function get_option($answer, $tolerance_type)
    {
        foreach ($this->get_options() as $option)
        {
            if ($option->matches($answer, $tolerance_type))
            {
                return $option;
            }
        }

        return null;
    }

    /**
     * @return AssessmentMatchNumericQuestionOption[]
     */
    public function get_options()
    {
        if ($result = unserialize($this->get_additional_property(self::PROPERTY_OPTIONS)))
        {
            return $result;
        }

        return [];
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'repository_assessment_match_numeric_question';
    }

    public function get_tolerance_type()
    {
        return $this->get_additional_property(self::PROPERTY_TOLERANCE_TYPE);
    }

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class, true);
    }

    /**
     *
     * @return boolean
     */
    public function has_feedback()
    {
        foreach ($this->get_options() as $option)
        {
            if ($option->has_feedback())
            {
                return true;
            }
        }

        return false;
    }

    // TODO: should be moved to an additional parent layer "question" which offers a default implementation.

    public function has_hint()
    {
        return StringUtilities::getInstance()->hasValue($this->get_hint(), true);
    }

    public function set_hint($hint)
    {
        return $this->set_additional_property(self::PROPERTY_HINT, $hint);
    }

    public function set_options($options)
    {
        return $this->set_additional_property(self::PROPERTY_OPTIONS, serialize($options));
    }

    public function set_tolerance_type($type)
    {
        return $this->set_additional_property(self::PROPERTY_TOLERANCE_TYPE, $type);
    }
}
