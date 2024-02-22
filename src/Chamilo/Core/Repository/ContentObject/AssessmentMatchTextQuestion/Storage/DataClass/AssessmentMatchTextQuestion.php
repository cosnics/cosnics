<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\VersionableInterface;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassExtensionInterface;
use Chamilo\Libraries\Storage\DataClass\Traits\DataClassExtensionTrait;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion\Storage\DataClass
 */
class AssessmentMatchTextQuestion extends ContentObject implements VersionableInterface, DataClassExtensionInterface
{
    use DataClassExtensionTrait;

    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion';

    public const PROPERTY_HINT = 'hint';
    public const PROPERTY_IGNORE_CASE = 'ignore_case';
    public const PROPERTY_OPTIONS = 'options';
    public const PROPERTY_USE_WILDCARDS = 'use_wildcards';

    public function add_option($option)
    {
        $options = $this->get_options();
        $options[] = $option;

        return $this->setAdditionalProperty(self::PROPERTY_OPTIONS, serialize($options));
    }

    public static function getAdditionalPropertyNames(): array
    {
        return [
            self::PROPERTY_OPTIONS,
            self::PROPERTY_USE_WILDCARDS,
            self::PROPERTY_IGNORE_CASE,
            self::PROPERTY_HINT
        ];
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

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_assessment_match_text_question';
    }

    public function get_best_option()
    {
        return $this->getBestOptions()[0];
    }

    public function get_default_weight()
    {
        return $this->get_maximum_score();
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
        return parent::get_html_editors([self::PROPERTY_HINT]);
    }

    public function get_ignore_case()
    {
        return $this->getAdditionalProperty(self::PROPERTY_IGNORE_CASE);
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

    /**
     * @return AssessmentMatchTextQuestionOption[]
     */
    public function get_options()
    {
        if ($result = unserialize($this->getAdditionalProperty(self::PROPERTY_OPTIONS)))
        {
            return $result;
        }

        return [];
    }

    public function get_use_wildcards()
    {
        return $this->getAdditionalProperty(self::PROPERTY_USE_WILDCARDS);
    }

    /**
     * @return bool
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
        return $this->setAdditionalProperty(self::PROPERTY_HINT, $hint);
    }

    public function set_ignore_case($type)
    {
        return $this->setAdditionalProperty(self::PROPERTY_IGNORE_CASE, (bool) $type);
    }

    public function set_options($options)
    {
        return $this->setAdditionalProperty(self::PROPERTY_OPTIONS, serialize($options));
    }

    public function set_use_wildcards($type)
    {
        return $this->setAdditionalProperty(self::PROPERTY_USE_WILDCARDS, (bool) $type);
    }
}
