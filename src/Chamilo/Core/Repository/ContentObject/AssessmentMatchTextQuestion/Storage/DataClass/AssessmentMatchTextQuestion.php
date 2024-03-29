<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Storage\DataClass\AssessmentMultipleChoiceQuestionOption;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\Platform\Security\SerializedDataValidator;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.lib.content_object.match_text_question
 */
class AssessmentMatchTextQuestion extends ContentObject implements Versionable
{
    const PROPERTY_OPTIONS = 'options';
    const PROPERTY_USE_WILDCARDS = 'use_wildcards';
    const PROPERTY_IGNORE_CASE = 'ignore_case';
    const PROPERTY_HINT = 'hint';

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public static function get_additional_property_names()
    {
        return array(
            self::PROPERTY_OPTIONS, 
            self::PROPERTY_USE_WILDCARDS, 
            self::PROPERTY_IGNORE_CASE, 
            self::PROPERTY_HINT);
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
     * @return AssessmentMatchTextQuestionOption[]
     */
    public function get_options()
    {
        $serializedOptions = $this->get_additional_property(self::PROPERTY_OPTIONS);

        SerializedDataValidator::validateSerializedData($serializedOptions, [AssessmentMatchTextQuestionOption::class]);

        if ($result = unserialize($serializedOptions))
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

    public function set_use_wildcards($type)
    {
        return $this->set_additional_property(self::PROPERTY_USE_WILDCARDS, (bool) $type);
    }

    public function get_use_wildcards()
    {
        return $this->get_additional_property(self::PROPERTY_USE_WILDCARDS);
    }

    public function set_ignore_case($type)
    {
        return $this->set_additional_property(self::PROPERTY_IGNORE_CASE, (bool) $type);
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

    public function get_ignore_case()
    {
        return $this->get_additional_property(self::PROPERTY_IGNORE_CASE);
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
        return $this->getBestOptions()[0];
    }
    
    public function getBestOptions()
    {
        $bestScore = null;
        $bestOptions = [];

        foreach ($this->get_options() as $key => $option)
        {
            if (is_null($bestScore) || $option->get_score() >= $bestScore)
            {
                $bestScore = $option->get_score();
                $bestOptions[] = $option;
            }
        }

        return $bestOptions;
    }

    public function get_option($answer, $ignore_case, $use_wildcards)
    {
        foreach ($this->get_options() as $option)
        {
            if ($option->matches($answer, $ignore_case, $use_wildcards))
            {
                return $option;
            }
        }
        
        return null;
    }
}
