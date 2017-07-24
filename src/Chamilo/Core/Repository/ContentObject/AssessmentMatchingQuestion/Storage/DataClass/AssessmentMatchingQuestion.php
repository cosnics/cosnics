<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 * $Id: assessment_matching_question.class.php $
 * 
 * @package repository.lib.content_object.matching_question
 */
class AssessmentMatchingQuestion extends ContentObject implements Versionable
{
    const PROPERTY_DISPLAY = 'display';
    const PROPERTY_OPTIONS = 'options';
    const PROPERTY_MATCHES = 'matches';
    const DISPLAY_LIST = 1;
    const DISPLAY_SELECT = 2;

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
     * @return AssessmentMatchingQuestionOption[]
     */
    public function get_options()
    {
        if ($result = unserialize($this->get_additional_property(self::PROPERTY_OPTIONS)))
        {
            return $result;
        }
        return array();
    }

    public function get_number_of_options()
    {
        return count($this->get_options());
    }

    public function add_match($match)
    {
        $matches = $this->get_matches();
        $matches[] = $match;
        return $this->set_additional_property(self::PROPERTY_MATCHES, serialize($matches));
    }

    public function set_matches($matches)
    {
        return $this->set_additional_property(self::PROPERTY_MATCHES, serialize($matches));
    }

    public function get_matches()
    {
        if ($result = unserialize($this->get_additional_property(self::PROPERTY_MATCHES)))
        {
            return $result;
        }
        return array();
    }

    public function get_number_of_matches()
    {
        return count($this->get_matches());
    }

    public function set_display($display)
    {
        return $this->set_additional_property(self::PROPERTY_DISPLAY, $display);
    }

    public function get_display()
    {
        return $this->get_additional_property(self::PROPERTY_DISPLAY);
    }

    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_DISPLAY, self::PROPERTY_MATCHES, self::PROPERTY_OPTIONS);
    }

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
        ;
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
            $max += $option->get_score();
        }
        return $max;
    }
    
    // TODO: should be moved to an additional parent layer "question" which offers a default implementation.
    public function get_default_weight()
    {
        return $this->get_maximum_score();
    }
}
