<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Configuration;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Interfaces\HelperContentObjectSupport;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 * @package Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class LearningPathItem extends ContentObject implements Versionable, HelperContentObjectSupport
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\LearningPathItem';

    public const PROPERTY_ALLOW_HINTS = 'allow_hints';
    public const PROPERTY_FEEDBACK_LOCATION = 'feedback_location';
    public const PROPERTY_MASTERY_SCORE = 'mastery_score';
    public const PROPERTY_MAX_ATTEMPTS = 'max_attempts';
    public const PROPERTY_REFERENCE = 'reference_id';
    public const PROPERTY_SHOW_ANSWER_FEEDBACK = 'show_answer_feedback';
    public const PROPERTY_SHOW_CORRECTION = 'show_correction';
    public const PROPERTY_SHOW_SCORE = 'show_score';
    public const PROPERTY_SHOW_SOLUTION = 'show_solution';

    /**
     * @var \core\repository\content_object\learning_path\LearningPath
     */
    private $reference_object;

    /**
     * @return string[]
     */
    public static function getAdditionalPropertyNames(): array
    {
        return [
            self::PROPERTY_REFERENCE,
            self::PROPERTY_MAX_ATTEMPTS,
            self::PROPERTY_MASTERY_SCORE,
            self::PROPERTY_ALLOW_HINTS,
            self::PROPERTY_SHOW_SCORE,
            self::PROPERTY_SHOW_CORRECTION,
            self::PROPERTY_SHOW_SOLUTION,
            self::PROPERTY_SHOW_ANSWER_FEEDBACK,
            self::PROPERTY_FEEDBACK_LOCATION
        ];
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_learning_path_item';
    }

    /**
     * @return bool
     */
    public function get_allow_hints()
    {
        return $this->getAdditionalProperty(self::PROPERTY_ALLOW_HINTS);
    }

    /**
     * @return \core\repository\content_object\assessment\Configuration
     */
    public function get_configuration()
    {
        return new Configuration(
            $this->get_allow_hints(), $this->get_show_score(), $this->get_show_correction(), $this->get_show_solution(),
            $this->get_show_answer_feedback(), $this->get_feedback_location()
        );
    }

    /**
     * @return int
     */
    public function get_feedback_location()
    {
        return $this->getAdditionalProperty(self::PROPERTY_FEEDBACK_LOCATION);
    }

    /**
     * @return int
     */
    public function get_mastery_score()
    {
        return $this->getAdditionalProperty(self::PROPERTY_MASTERY_SCORE);
    }

    /**
     * @return int
     */
    public function get_max_attempts()
    {
        return $this->getAdditionalProperty(self::PROPERTY_MAX_ATTEMPTS);
    }

    /**
     * @return int
     */
    public function get_reference()
    {
        return $this->getAdditionalProperty(self::PROPERTY_REFERENCE);
    }

    /**
     * @return \core\repository\content_object\learning_path\LearningPath
     */
    public function get_reference_object()
    {
        if (!$this->reference_object instanceof LearningPath)
        {
            $this->reference_object = DataManager::retrieve_by_id(
                ContentObject::class, $this->get_reference()
            );
        }

        return $this->reference_object;
    }

    /**
     * @return int
     */
    public function get_show_answer_feedback()
    {
        return $this->getAdditionalProperty(self::PROPERTY_SHOW_ANSWER_FEEDBACK);
    }

    /**
     * @return bool
     */
    public function get_show_correction()
    {
        return $this->getAdditionalProperty(self::PROPERTY_SHOW_CORRECTION);
    }

    /**
     * @return bool
     */
    public function get_show_score()
    {
        return $this->getAdditionalProperty(self::PROPERTY_SHOW_SCORE);
    }

    /**
     * @return bool
     */
    public function get_show_solution()
    {
        return $this->getAdditionalProperty(self::PROPERTY_SHOW_SOLUTION);
    }

    /**
     * @param bool $allow_hints
     */
    public function set_allow_hints($allow_hints)
    {
        $this->setAdditionalProperty(self::PROPERTY_ALLOW_HINTS, $allow_hints);
    }

    /**
     * @param int $feedback_location
     */
    public function set_feedback_location($feedback_location)
    {
        $this->setAdditionalProperty(self::PROPERTY_FEEDBACK_LOCATION, $feedback_location);
    }

    /**
     * @param int $mastery_score
     */
    public function set_mastery_score($mastery_score)
    {
        $this->setAdditionalProperty(self::PROPERTY_MASTERY_SCORE, $mastery_score);
    }

    /**
     * @param int $max_attempts
     */
    public function set_max_attempts($max_attempts)
    {
        $this->setAdditionalProperty(self::PROPERTY_MAX_ATTEMPTS, $max_attempts);
    }

    /**
     * @param int $reference
     */
    public function set_reference($reference)
    {
        $this->setAdditionalProperty(self::PROPERTY_REFERENCE, $reference);
    }

    /**
     * @param int $show_answer_feedback
     */
    public function set_show_answer_feedback($show_answer_feedback)
    {
        $this->setAdditionalProperty(self::PROPERTY_SHOW_ANSWER_FEEDBACK, $show_answer_feedback);
    }

    /**
     * @param bool $show_correction
     */
    public function set_show_correction($show_correction)
    {
        $this->setAdditionalProperty(self::PROPERTY_SHOW_CORRECTION, $show_correction);
    }

    /**
     * @param bool $show_score
     */
    public function set_show_score($show_score)
    {
        $this->setAdditionalProperty(self::PROPERTY_SHOW_SCORE, $show_score);
    }

    /**
     * @param bool $show_solution
     */
    public function set_show_solution($show_solution)
    {
        $this->setAdditionalProperty(self::PROPERTY_SHOW_SOLUTION, $show_solution);
    }
}
