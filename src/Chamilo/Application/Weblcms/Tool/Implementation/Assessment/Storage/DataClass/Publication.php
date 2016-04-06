<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataClass;

use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Configuration;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package application\weblcms\tool\implementation\assessment
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Publication extends DataClass
{

    /**
     * Assessment publication properties
     */
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_ALLOW_HINTS = 'allow_hints';
    const PROPERTY_SHOW_SCORE = 'show_score';
    const PROPERTY_SHOW_CORRECTION = 'show_correction';
    const PROPERTY_SHOW_SOLUTION = 'show_solution';
    const PROPERTY_SHOW_ANSWER_FEEDBACK = 'show_answer_feedback';
    const PROPERTY_FEEDBACK_LOCATION = 'feedback_location';

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_PUBLICATION_ID,
                self :: PROPERTY_ALLOW_HINTS,
                self :: PROPERTY_SHOW_SCORE,
                self :: PROPERTY_SHOW_CORRECTION,
                self :: PROPERTY_SHOW_SOLUTION,
                self :: PROPERTY_SHOW_ANSWER_FEEDBACK,
                self :: PROPERTY_FEEDBACK_LOCATION));
    }

    public function get_data_manager()
    {
        return DataManager :: get_instance();
    }

    public function get_publication_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID);
    }

    public function set_publication_id($publication_id)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_ID, $publication_id);
    }

    /**
     *
     * @return boolean
     */
    public function get_allow_hints()
    {
        return $this->get_default_property(self :: PROPERTY_ALLOW_HINTS);
    }

    /**
     *
     * @param boolean $allow_hints
     */
    public function set_allow_hints($allow_hints)
    {
        $this->set_default_property(self :: PROPERTY_ALLOW_HINTS, $allow_hints);
    }

    /**
     *
     * @return boolean
     */
    public function get_show_score()
    {
        return $this->get_default_property(self :: PROPERTY_SHOW_SCORE);
    }

    /**
     *
     * @param boolean $show_score
     */
    public function set_show_score($show_score)
    {
        $this->set_default_property(self :: PROPERTY_SHOW_SCORE, $show_score);
    }

    /**
     *
     * @return boolean
     */
    public function get_show_correction()
    {
        return $this->get_default_property(self :: PROPERTY_SHOW_CORRECTION);
    }

    /**
     *
     * @param boolean $show_correction
     */
    public function set_show_correction($show_correction)
    {
        $this->set_default_property(self :: PROPERTY_SHOW_CORRECTION, $show_correction);
    }

    /**
     *
     * @return boolean
     */
    public function get_show_solution()
    {
        return $this->get_default_property(self :: PROPERTY_SHOW_SOLUTION);
    }

    /**
     *
     * @param boolean $show_solution
     */
    public function set_show_solution($show_solution)
    {
        $this->set_default_property(self :: PROPERTY_SHOW_SOLUTION, $show_solution);
    }

    /**
     *
     * @return int
     */
    public function get_show_answer_feedback()
    {
        return $this->get_default_property(self :: PROPERTY_SHOW_ANSWER_FEEDBACK);
    }

    /**
     *
     * @param int $show_answer_feedback
     */
    public function set_show_answer_feedback($show_answer_feedback)
    {
        $this->set_default_property(self :: PROPERTY_SHOW_ANSWER_FEEDBACK, $show_answer_feedback);
    }

    /**
     *
     * @return int
     */
    public function get_feedback_location()
    {
        return $this->get_default_property(self :: PROPERTY_FEEDBACK_LOCATION);
    }

    /**
     *
     * @param int $feedback_location
     */
    public function set_feedback_location($feedback_location)
    {
        $this->set_default_property(self :: PROPERTY_FEEDBACK_LOCATION, $feedback_location);
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
