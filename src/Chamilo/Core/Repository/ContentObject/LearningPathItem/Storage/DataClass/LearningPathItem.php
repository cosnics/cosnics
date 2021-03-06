<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Configuration;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\HelperContentObjectSupport;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 *
 * @package core\repository\content_object\learning_path_item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LearningPathItem extends ContentObject implements Versionable, HelperContentObjectSupport
{
    const PROPERTY_REFERENCE = 'reference_id';
    const PROPERTY_MAX_ATTEMPTS = 'max_attempts';
    const PROPERTY_MASTERY_SCORE = 'mastery_score';
    const PROPERTY_ALLOW_HINTS = 'allow_hints';
    const PROPERTY_SHOW_SCORE = 'show_score';
    const PROPERTY_SHOW_CORRECTION = 'show_correction';
    const PROPERTY_SHOW_SOLUTION = 'show_solution';
    const PROPERTY_SHOW_ANSWER_FEEDBACK = 'show_answer_feedback';
    const PROPERTY_FEEDBACK_LOCATION = 'feedback_location';

    /**
     *
     * @var \core\repository\content_object\learning_path\LearningPath
     */
    private $reference_object;

    /**
     *
     * @return string
     */
    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    /**
     *
     * @return string[]
     */
    public static function get_additional_property_names()
    {
        return array(
            self::PROPERTY_REFERENCE, 
            self::PROPERTY_MAX_ATTEMPTS, 
            self::PROPERTY_MASTERY_SCORE, 
            self::PROPERTY_ALLOW_HINTS, 
            self::PROPERTY_SHOW_SCORE, 
            self::PROPERTY_SHOW_CORRECTION, 
            self::PROPERTY_SHOW_SOLUTION, 
            self::PROPERTY_SHOW_ANSWER_FEEDBACK, 
            self::PROPERTY_FEEDBACK_LOCATION);
    }

    /**
     *
     * @return int
     */
    public function get_reference()
    {
        return $this->get_additional_property(self::PROPERTY_REFERENCE);
    }

    /**
     *
     * @param int $reference
     */
    public function set_reference($reference)
    {
        $this->set_additional_property(self::PROPERTY_REFERENCE, $reference);
    }

    /**
     *
     * @return \core\repository\content_object\learning_path\LearningPath
     */
    public function get_reference_object()
    {
        if (! $this->reference_object instanceof LearningPath)
        {
            $this->reference_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $this->get_reference());
        }
        return $this->reference_object;
    }

    /**
     *
     * @return int
     */
    public function get_max_attempts()
    {
        return $this->get_additional_property(self::PROPERTY_MAX_ATTEMPTS);
    }

    /**
     *
     * @param int $max_attempts
     */
    public function set_max_attempts($max_attempts)
    {
        $this->set_additional_property(self::PROPERTY_MAX_ATTEMPTS, $max_attempts);
    }

    /**
     *
     * @return int
     */
    public function get_mastery_score()
    {
        return $this->get_additional_property(self::PROPERTY_MASTERY_SCORE);
    }

    /**
     *
     * @param int $mastery_score
     */
    public function set_mastery_score($mastery_score)
    {
        $this->set_additional_property(self::PROPERTY_MASTERY_SCORE, $mastery_score);
    }

    /**
     *
     * @return boolean
     */
    public function get_allow_hints()
    {
        return $this->get_additional_property(self::PROPERTY_ALLOW_HINTS);
    }

    /**
     *
     * @param boolean $allow_hints
     */
    public function set_allow_hints($allow_hints)
    {
        $this->set_additional_property(self::PROPERTY_ALLOW_HINTS, $allow_hints);
    }

    /**
     *
     * @return boolean
     */
    public function get_show_score()
    {
        return $this->get_additional_property(self::PROPERTY_SHOW_SCORE);
    }

    /**
     *
     * @param boolean $show_score
     */
    public function set_show_score($show_score)
    {
        $this->set_additional_property(self::PROPERTY_SHOW_SCORE, $show_score);
    }

    /**
     *
     * @return boolean
     */
    public function get_show_correction()
    {
        return $this->get_additional_property(self::PROPERTY_SHOW_CORRECTION);
    }

    /**
     *
     * @param boolean $show_correction
     */
    public function set_show_correction($show_correction)
    {
        $this->set_additional_property(self::PROPERTY_SHOW_CORRECTION, $show_correction);
    }

    /**
     *
     * @return boolean
     */
    public function get_show_solution()
    {
        return $this->get_additional_property(self::PROPERTY_SHOW_SOLUTION);
    }

    /**
     *
     * @param boolean $show_solution
     */
    public function set_show_solution($show_solution)
    {
        $this->set_additional_property(self::PROPERTY_SHOW_SOLUTION, $show_solution);
    }

    /**
     *
     * @return int
     */
    public function get_show_answer_feedback()
    {
        return $this->get_additional_property(self::PROPERTY_SHOW_ANSWER_FEEDBACK);
    }

    /**
     *
     * @param int $show_answer_feedback
     */
    public function set_show_answer_feedback($show_answer_feedback)
    {
        $this->set_additional_property(self::PROPERTY_SHOW_ANSWER_FEEDBACK, $show_answer_feedback);
    }

    /**
     *
     * @return int
     */
    public function get_feedback_location()
    {
        return $this->get_additional_property(self::PROPERTY_FEEDBACK_LOCATION);
    }

    /**
     *
     * @param int $feedback_location
     */
    public function set_feedback_location($feedback_location)
    {
        $this->set_additional_property(self::PROPERTY_FEEDBACK_LOCATION, $feedback_location);
    }

    /**
     *
     * @return \core\repository\content_object\assessment\Configuration
     */
    public function get_configuration()
    {
        return new Configuration(
            $this->get_allow_hints(), 
            $this->get_show_score(), 
            $this->get_show_correction(), 
            $this->get_show_solution(), 
            $this->get_show_answer_feedback(), 
            $this->get_feedback_location());
    }
}
