<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 * @package Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Storage\DataClass
 */
class AssessmentMatchingQuestion extends ContentObject implements Versionable
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion';

    public const DISPLAY_LIST = 1;
    public const DISPLAY_SELECT = 2;

    public const PROPERTY_DISPLAY = 'display';
    public const PROPERTY_MATCHES = 'matches';
    public const PROPERTY_OPTIONS = 'options';

    public function add_match($match)
    {
        $matches = $this->get_matches();
        $matches[] = $match;

        return $this->setAdditionalProperty(self::PROPERTY_MATCHES, serialize($matches));
    }

    public function add_option($option)
    {
        $options = $this->get_options();
        $options[] = $option;

        return $this->setAdditionalProperty(self::PROPERTY_OPTIONS, serialize($options));
    }

    public static function getAdditionalPropertyNames(): array
    {
        return [self::PROPERTY_DISPLAY, self::PROPERTY_MATCHES, self::PROPERTY_OPTIONS];
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_assessment_matching_question';
    }

    public function get_default_weight()
    {
        return $this->get_maximum_score();
    }

    public function get_display()
    {
        return $this->getAdditionalProperty(self::PROPERTY_DISPLAY);
    }

    public function get_matches()
    {
        if ($result = unserialize($this->getAdditionalProperty(self::PROPERTY_MATCHES)))
        {
            return $result;
        }

        return [];
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

    public function get_number_of_matches()
    {
        return count($this->get_matches());
    }

    public function get_number_of_options()
    {
        return count($this->get_options());
    }

    /**
     * @return AssessmentMatchingQuestionOption[]
     */
    public function get_options()
    {
        if ($result = unserialize($this->getAdditionalProperty(self::PROPERTY_OPTIONS)))
        {
            return $result;
        }

        return [];
    }

    public function set_display($display)
    {
        return $this->setAdditionalProperty(self::PROPERTY_DISPLAY, $display);
    }

    public function set_matches($matches)
    {
        return $this->setAdditionalProperty(self::PROPERTY_MATCHES, serialize($matches));
    }

    // TODO: should be moved to an additional parent layer "question" which offers a default implementation.

    public function set_options($options)
    {
        return $this->setAdditionalProperty(self::PROPERTY_OPTIONS, serialize($options));
    }
}
