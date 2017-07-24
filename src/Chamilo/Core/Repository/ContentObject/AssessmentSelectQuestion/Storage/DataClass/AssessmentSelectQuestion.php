<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * $Id: assessment_select_question.class.php $
 * 
 * @package repository.lib.content_object.select_question
 */
class AssessmentSelectQuestion extends ContentObject
{
    const PROPERTY_OPTIONS = 'options';
    const PROPERTY_ANSWER_TYPE = 'answer_type';
    const PROPERTY_HINT = 'hint';
    const ANSWER_TYPE_CHECKBOX = 'checkbox';
    const ANSWER_TYPE_RADIO = 'radio';

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
     * @return AssessmentSelectQuestionOption[]
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

    public function get_answer_type()
    {
        return $this->get_additional_property(self::PROPERTY_ANSWER_TYPE);
    }

    public function set_answer_type($answer_type)
    {
        return $this->set_additional_property(self::PROPERTY_ANSWER_TYPE, $answer_type);
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

    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_ANSWER_TYPE, self::PROPERTY_OPTIONS, self::PROPERTY_HINT);
    }

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    /**
     * Returns the maximum weight/score a user can receive.
     */
    public function get_maximum_score()
    {
        $max = 0;
        
        switch ($this->get_answer_type())
        {
            case self::ANSWER_TYPE_CHECKBOX :
                foreach ($this->get_options() as $option)
                {
                    if ($option->is_correct())
                    {
                        $max += $option->get_score();
                    }
                }
                break;
            case self::ANSWER_TYPE_RADIO :
                foreach ($this->get_options() as $option)
                {
                    if ($option->is_correct())
                    {
                        $max = max($max, $option->get_score());
                    }
                }
                break;
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
}
