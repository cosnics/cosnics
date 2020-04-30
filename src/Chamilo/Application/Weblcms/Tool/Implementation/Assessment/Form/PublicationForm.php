<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Form;

use Chamilo\Application\Weblcms\Form\ContentObjectPublicationForm;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataClass\Publication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Configuration;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Form\ConfigurationForm;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 * Extension of the default Content to allow for the feedback-functionality
 *
 * @author Hans De Bisschop
 */
class PublicationForm extends ContentObjectPublicationForm
{

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer $form_type
     * @param ContentObjectPublication[] $publications
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param string $action
     * @param boolean $is_course_admin
     *
     * @param array $selectedContentObjects
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     */
    public function __construct(
        User $user, $form_type, $publications, $course, $action, $is_course_admin, $selectedContentObjects = array()
    )
    {
        parent::__construct(
            'Chamilo\Application\Weblcms\Tool\Implementation\Assessment', $user, $form_type, $publications, $course,
            $action, $is_course_admin, $selectedContentObjects
        );

        $publications = $this->get_publications();

        if (count($publications) == 1 && $this->get_form_type() == self::TYPE_UPDATE)
        {
            $first_publication = $publications[0];

            $parameters = new DataClassRetrieveParameters(
                new EqualityCondition(
                    new PropertyConditionVariable(Publication::class, Publication::PROPERTY_PUBLICATION_ID),
                    new StaticConditionVariable($first_publication->get_id())
                )
            );
            $assessment_publication = DataManager::retrieve(Publication::class, $parameters);
            $configuration = $assessment_publication->get_configuration();
        }
        else
        {
            $configuration = new Configuration();
        }

        ConfigurationForm::defaults($this, $configuration);
    }

    /**
     * Builds the basic create form (without buttons)
     */
    public function build_basic_create_form()
    {
        $this->addElement('category', Translation::get('DefaultProperties'));
        parent::build_basic_create_form();

        ConfigurationForm::build($this);
    }

    /**
     * Builds the basic update form (without buttons)
     */
    public function build_basic_update_form()
    {
        $this->addElement('category', Translation::get('DefaultProperties'));
        parent::build_basic_update_form();

        ConfigurationForm::build($this);
    }

    /**
     * Handles the submit of the form for both create and edit
     *
     * @return boolean
     */
    public function handle_form_submit()
    {
        $values = $this->exportValues();
        $success = parent::handle_form_submit();

        $allow_hints = isset($values[Configuration::PROPERTY_ALLOW_HINTS]) ? 1 : 0;
        $show_score = isset($values[Configuration::PROPERTY_SHOW_SCORE]) ? 1 : 0;
        $show_correction = isset($values[Configuration::PROPERTY_SHOW_CORRECTION]) ? 1 : 0;
        $show_solution = isset($values[Configuration::PROPERTY_SHOW_SOLUTION]) ? 1 : 0;

        if (isset($values[ConfigurationForm::PROPERTY_ANSWER_FEEDBACK_OPTION]))
        {
            $show_answer_feedback = $values[Configuration::PROPERTY_SHOW_ANSWER_FEEDBACK];
        }
        else
        {
            $show_answer_feedback = Configuration::ANSWER_FEEDBACK_TYPE_NONE;
        }

        if ($show_score || $show_correction || $show_solution || $show_answer_feedback)
        {
            $feedback_location = $values[Configuration::PROPERTY_FEEDBACK_LOCATION];
        }
        else
        {
            $feedback_location = Configuration::FEEDBACK_LOCATION_TYPE_NONE;
        }

        if ($success)
        {
            $publications = $this->get_publications();
            $succes = true;

            foreach ($publications as $publication)
            {
                switch ($this->get_form_type())
                {
                    case self::TYPE_CREATE :
                        $assessment_publication = new Publication();
                        $assessment_publication->set_publication_id($publication->get_id());
                        $assessment_publication->set_allow_hints($allow_hints);
                        $assessment_publication->set_show_score($show_score);
                        $assessment_publication->set_show_correction($show_correction);
                        $assessment_publication->set_show_solution($show_solution);
                        $assessment_publication->set_show_answer_feedback($show_answer_feedback);
                        $assessment_publication->set_feedback_location($feedback_location);
                        $succes &= $assessment_publication->create();
                        break;
                    case self::TYPE_UPDATE :
                        $parameters = new DataClassRetrieveParameters(
                            new EqualityCondition(
                                new PropertyConditionVariable(
                                    Publication::class_name(), Publication::PROPERTY_PUBLICATION_ID
                                ), new StaticConditionVariable($publication->get_id())
                            )
                        );
                        $assessment_publication = DataManager::retrieve(Publication::class, $parameters);

                        $assessment_publication->set_allow_hints($allow_hints);
                        $assessment_publication->set_show_score($show_score);
                        $assessment_publication->set_show_correction($show_correction);
                        $assessment_publication->set_show_solution($show_solution);
                        $assessment_publication->set_show_answer_feedback($show_answer_feedback);
                        $assessment_publication->set_feedback_location($feedback_location);
                        $succes &= $assessment_publication->update();
                        break;
                }
            }

            return $succes;
        }
        else
        {
            return false;
        }
    }
}
