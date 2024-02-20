<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\VersionableInterface;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassExtensionInterface;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion\Storage\DataClass
 */
class AssessmentRatingQuestion extends ContentObject
    implements VersionableInterface, DataClassExtensionInterface
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion';

    public const PROPERTY_CORRECT = 'correct';
    public const PROPERTY_FEEDBACK = 'feedback';
    public const PROPERTY_HIGH = 'high';
    public const PROPERTY_HINT = 'hint';
    public const PROPERTY_LOW = 'low';

    public static function getAdditionalPropertyNames(): array
    {
        return parent::getAdditionalPropertyNames([
            self::PROPERTY_LOW,
            self::PROPERTY_HIGH,
            self::PROPERTY_CORRECT,
            self::PROPERTY_FEEDBACK,
            self::PROPERTY_HINT
        ]);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_assessment_rating_question';
    }

    public function get_correct()
    {
        return $this->getAdditionalProperty(self::PROPERTY_CORRECT);
    }

    public function get_feedback()
    {
        return $this->getAdditionalProperty(self::PROPERTY_FEEDBACK);
    }

    public function get_high()
    {
        return $this->getAdditionalProperty(self::PROPERTY_HIGH);
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

    public function get_low()
    {
        return $this->getAdditionalProperty(self::PROPERTY_LOW);
    }

    public function has_hint()
    {
        return StringUtilities::getInstance()->hasValue($this->get_hint(), true);
    }

    public function set_correct($value)
    {
        $this->setAdditionalProperty(self::PROPERTY_CORRECT, $value);
    }

    public function set_feedback($feedback)
    {
        $this->setAdditionalProperty(self::PROPERTY_FEEDBACK, $feedback);
    }

    public function set_high($value)
    {
        $this->setAdditionalProperty(self::PROPERTY_HIGH, $value);
    }

    public function set_hint($hint)
    {
        return $this->setAdditionalProperty(self::PROPERTY_HINT, $hint);
    }

    public function set_low($value)
    {
        $this->setAdditionalProperty(self::PROPERTY_LOW, $value);
    }
}
