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
    const PROPERTY_OPTIONS = 'options';
    const PROPERTY_TOLERANCE_TYPE = 'tolerance_type';
    const PROPERTY_HINT = 'hint';
    const TOLERANCE_TYPE_ABSOLUTE = 'absolute';
    const TOLERANCE_TYPE_RELATIVE = 'relative';

    public function __construct($defaultProperties = array (), $additionalProperties = null)
    {
        parent::__construct($defaultProperties, $additionalProperties);
        if (! isset($additionalProperties[self::PROPERTY_TOLERANCE_TYPE]))
        {
            $this->set_tolerance_type(self::TOLERANCE_TYPE_ABSOLUTE);
        }
    }

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_TOLERANCE_TYPE, self::PROPERTY_OPTIONS, self::PROPERTY_HINT);
    }

    public function add_option($option)
    {
        $options = $this->get_options();
        $options[] = $option;
        return $this->set_additional_property(self::PROPERTY_OPTIONS, serialize($options));
    }

    public function set_options($options)
    {
        return $this->set_additional_property(self::PROPERTY_OPTIONS, serialize($options));
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
        return array();
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

    public function get_number_of_options()
    {
        return count($this->get_options());
    }

    public function set_tolerance_type($type)
    {
        return $this->set_additional_property(self::PROPERTY_TOLERANCE_TYPE, $type);
    }

    public function get_tolerance_type()
    {
        return $this->get_additional_property(self::PROPERTY_TOLERANCE_TYPE);
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
    
    // TODO: should be moved to an additional parent layer "question" which offers a default implementation.
    public function get_default_weight()
    {
        return $this->get_maximum_score();
    }

    /**
     * Returns the names of the properties which are UI-wise filled by the integrated html editor
     * 
     * @return multitype:string
     */
    public static function get_html_editors($html_editors = array())
    {
        return parent::get_html_editors(array(self::PROPERTY_HINT));
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
}
