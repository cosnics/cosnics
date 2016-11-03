<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionDetail;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This form allows a user to give or update the score of a submission.
 *
 * @author Bert De Clercq (Hogeschool Gent)
 */
class SubmissionDetailScoreSection extends FormValidator
{

    /**
     * Caching variable for the score tracker.
     *
     * @var \application\weblcms\integration\core\tracking\tracker\SubmissionScore The score tracker
     */
    private $score_tracker;

    /**
     * Fills the form with all the data.
     *
     * @param $score_tracker \application\weblcms\integration\core\tracking\tracker\SubmissionScore
     * @param $url string The url of the main page
     */
    public function __construct($score_tracker, $url = '')
    {
        parent :: __construct('submission_score', 'post', $url);
        $this->score_tracker = $score_tracker;

        $choices = array();

        $choices[- 1] = Translation :: get('NoScore');
        for ($i = 0; $i <= 100; $i ++)
        {
            $choices[$i] = $i . '%';
        }

        $this->addElement(
            'select',
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: PROPERTY_SCORE,
            Translation :: get('Score'),
            $choices);

        $save_button = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Save', null, Utilities :: COMMON_LIBRARIES),
            null,
            null,
            'arrow-right');

        $this->addElement($save_button);

        $this->set_initial_score();
    }

    /**
     * Sets the score on the form when it is rendered.
     */
    private function set_initial_score()
    {
        if ($this->score_tracker)
        {
            $defaults = array();
            $defaults[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: PROPERTY_SCORE] = $this->score_tracker->get_score();
            parent :: setDefaults($defaults);
        }
    }

    /**
     * Updates or creates the score tracker of the submission.
     */
    public function set_score()
    {
        if ($this->score_tracker)
        {
            $this->update_tracker();
        }
        else
        {
            $this->create_tracker();
        }
    }

    /**
     * Creates a score tracker for the submission.
     *
     * @return boolean
     */
    private function create_tracker()
    {
        $values = $this->exportValues();

        if ($values[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: PROPERTY_SCORE] ==
             - 1)
        {
            return false;
        }

        $arguments = array(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: PROPERTY_SUBMISSION_ID => Request :: get(
                Manager :: PARAM_SUBMISSION),
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: PROPERTY_CREATED => time(),
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: PROPERTY_MODIFIED => time(),
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: PROPERTY_USER_ID => Request :: get(
                Manager :: PARAM_TARGET_ID),
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: PROPERTY_SCORE => $values[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: PROPERTY_SCORE]);
        Event :: trigger('ScoreSubmission', \Chamilo\Application\Weblcms\Manager :: context(), $arguments);

        return true;
    }

    /**
     * Updates the score tracker of the submission when a new score is assigned.
     *
     * @return boolean
     */
    private function update_tracker()
    {
        $values = $this->exportValues();

        if ($values[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: PROPERTY_SCORE] ==
             - 1)
        {
            return $this->score_tracker->delete();
        }

        $score = $values[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: PROPERTY_SCORE];
        $this->score_tracker->set_score($score);
        $this->score_tracker->set_modified(time());

        return $this->score_tracker->update();
    }
}
