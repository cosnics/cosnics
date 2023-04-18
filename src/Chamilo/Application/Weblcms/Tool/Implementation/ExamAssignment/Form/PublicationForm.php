<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Form;

use Chamilo\Application\Weblcms\Form\ContentObjectPublicationForm;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\DataClass\Publication;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\Repository\PublicationRepository;
use Chamilo\Configuration\Service\RegistrationConsulter;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Form
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationForm extends ContentObjectPublicationForm
{
    const PROPERTY_USE_CODE = 'use_code';
    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\Repository\PublicationRepository
     */
    protected $publicationRepository;

    /**
     * @var \Chamilo\Configuration\Service\RegistrationConsulter
     */
    protected $registrationConsulter;

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer $form_type
     * @param ContentObjectPublication[] $publications
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param string $action
     * @param boolean $is_course_admin
     * @param array $selectedContentObjects
     *
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\Repository\PublicationRepository $publicationRepository
     *
     * @param \Chamilo\Configuration\Service\RegistrationConsulter $registrationConsulter
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     */
    public function __construct(
        User $user, $form_type, $publications, $course, $action, $is_course_admin,
        $selectedContentObjects = array(), Translator $translator, PublicationRepository $publicationRepository,
        RegistrationConsulter $registrationConsulter
    )
    {
        $this->translator = $translator;
        $this->publicationRepository = $publicationRepository;
        $this->registrationConsulter = $registrationConsulter;

        parent::__construct(
            'Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment',
            $user,
            $form_type,
            $publications,
            $course,
            $action,
            $is_course_admin,
            $selectedContentObjects
        );

        if ($form_type == self::TYPE_UPDATE)
        {
            $this->setDefaultsForPublication($publications[0]);
        }
        else
        {
            $defaults = [];
            $defaults['feedback_period_choice'] = 0;
            $defaults['use_code'] = 0;
            $this->setDefaults($defaults);
        }
    }

    /**
     * Builds the basic create form (without buttons)
     *
     * @throws \HTML_QuickForm_Error
     * @throws \PEAR_Error
     */
    public function build_basic_create_form()
    {
        $this->addElement('category', $this->translator->trans('DefaultProperties', [], Manager::context()));
        parent::build_basic_create_form();
        $this->hideVisibility();
        $this->addAssignmentProperties();
    }

    /**
     * Builds the basic update form (without buttons)
     *
     * @throws \HTML_QuickForm_Error
     * @throws \PEAR_Error
     */
    public function build_basic_update_form()
    {
        $this->addElement('category', $this->translator->trans('DefaultProperties', [], Manager::context()));
        parent::build_basic_update_form();
        $this->hideVisibility();
        $this->addAssignmentProperties();
    }

    protected function hideVisibility()
    {
        $this->removeElement('hidden');
    }

    /**
     * @throws \HTML_QuickForm_Error
     * @throws \PEAR_Error
     */
    protected function addAssignmentProperties()
    {
        $this->addElement('category', $this->translator->trans('AssignmentProperties', [], Manager::context()));

        // Optional feedback period
        $this->add_feedback_period();

        $group = [];

        $group[] = $this->createElement(
            'radio', self::PROPERTY_USE_CODE, '', $this->translator->trans('NoCode', [], Manager::context()), 0,
            ['id' => 'no_code']
        );

        $group[] = $this->createElement(
            'radio', self::PROPERTY_USE_CODE, '', $this->translator->trans('UseCode', [], Manager::context()), 1,
            ['id' => 'use_code']
        );

        $group[] = $this->create_textfield(
            Publication::PROPERTY_CODE, $this->translator->trans('AccessCode', [], Manager::context()),
            [
                'type' => 'numeric', 'min' => 10000, 'max' => 99999, 'maxlength' => 5, 'minlength' => 5,
                'id' => 'access_code', 'autocomplete' => 'off'
            ]
        );

        $this->addGroup($group, null, $this->translator->trans('AccessCode', [], Manager::context()), '', false);

        $this->addElement(
            'html',
            "<script type=\"text/javascript\">
                $(document).ready(function() {
                   $('#use_code').on('click', function() {
                       $('#access_code').show();
                       $('#access_code').attr('required', 'required');
                   });
                   $('#no_code').on('click', function() {
                       $('#access_code').hide();
                       $('#access_code').removeAttr('required');
                   });
                   if ($('#no_code').is(':checked')) {
                      $('#access_code').hide();
                   }
                });
            </script>\n");
    }

    private function add_feedback_period()
    {
        $elementName = 'feedback_period_choice';

        $choices[] = $this->createElement(
            'radio',
            $elementName,
            '',
            $this->translator->trans('NoFeedbackPeriod', [], Manager::context()),
            0,
            array('id' => 'choice_feedback_none'));
        $choices[] = $this->createElement(
            'radio',
            $elementName,
            '',
            $this->translator->trans('FeedbackPeriodFromOnly', [], Manager::context()),
            1,
            array('id' => 'choice_feedback_from_only'));
        $choices[] = $this->createElement(
            'radio',
            $elementName,
            '',
            $this->translator->trans('FeedbackPeriodFromUntil', [], Manager::context()),
            2,
            array('id' => 'choice_feedback_from_until'));
        $this->addGroup($choices, null, $this->translator->trans('FeedbackPeriod', [], Manager::context()), '', false);

        $this->addElement('html', '<div style="margin-bottom:10px;margin-left:25px;display:block;" id="feedback_from_only">');
        $this->add_datepicker('feedback_from_date_only','');
        $this->addElement('html', '</div>');

        $this->addElement('html', '<div style="margin-bottom:10px;margin-left:25px;display:block;" id="feedback_from_until">');
        $this->add_datepicker('feedback_from_date', '');
        $this->add_datepicker('feedback_to_date', '');
        $this->addElement('html', '</div>');

        $this->addFormRule(array($this, 'check_document_form'));

        $this->addElement(
            'html',
            "<script type=\"text/javascript\">
                $(document).ready(function() {
                    $('#choice_feedback_none').on('click', function() {
                        $('#feedback_from_only').hide();
                        $('#feedback_from_until').hide();
                    });
                    $('#choice_feedback_from_only').on('click', function() {
                        $('#feedback_from_only').show();
                        $('#feedback_from_until').hide();
                    });
                    $('#choice_feedback_from_until').on('click', function() {
                        $('#feedback_from_only').hide();
                        $('#feedback_from_until').show();
                    });
                    if ($('#choice_feedback_none').is(':checked')) {
                         $('#feedback_from_only').hide();
                         $('#feedback_from_until').hide();
                    } else if ($('#choice_feedback_from_only').is(':checked')) {
                         $('#feedback_from_until').hide();
                    } else if ($('#choice_feedback_from_until').is(':checked')) {
                         $('#feedback_from_only').hide();
                    } 
                });
            </script>\n");
    }

    protected function check_document_form($fields)
    {
        $errors = array();
        if ($fields['feedback_period_choice'] == 2)
        {
            $exportValues = $this->exportValues();

            $from = DatetimeUtilities::time_from_datepicker($exportValues['feedback_from_date']);
            $to = DatetimeUtilities::time_from_datepicker($exportValues['feedback_to_date']);
            if ($from > $to)
            {
                $errors['feedback_from_date'] = $this->translator->trans('StartDateShouldBeBeforeEndDate', [], Manager::context());
            }
            elseif ($from == $to)
            {
                $errors['feedback_from_date'] = $this->translator->trans('StartDateShouldDifferFromEndDate', [], Manager::context());
            }
        }
        return $errors;
    }

        /**
     * Handles the submit of the form for both create and edit
     *
     * @return boolean
     *
     * @throws \HTML_QuickForm_Error
     */
    public function handle_form_submit()
    {
        if (!parent::handle_form_submit())
        {
            return false;
        }

        $publications = $this->get_publications();
        $success = true;

        foreach ($publications as $publication)
        {
            if ($this->get_form_type() == self::TYPE_CREATE)
            {
                $success &= $this->handleCreateAction($publication);
            }
            else
            {
                $success &= $this->handleUpdateAction($publication);
            }
        }

        return $success;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return bool
     *
     * @throws \HTML_QuickForm_Error
     */
    protected function handleCreateAction(ContentObjectPublication $contentObjectPublication)
    {
        $exportValues = $this->exportValues();

        $publication = new Publication();
        $publication->setPublicationId($contentObjectPublication->getId());
        $publication->setCode($exportValues[Publication::PROPERTY_CODE]);

        $this->handleFeedbackPeriod($exportValues, $publication);

        return $publication->create();
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return bool
     * @throws \HTML_QuickForm_Error
     */
    protected function handleUpdateAction(ContentObjectPublication $contentObjectPublication)
    {
        $exportValues = $this->exportValues();

        try
        {
            $publication =
                $this->publicationRepository->findPublicationByContentObjectPublication($contentObjectPublication);

            if ($exportValues[self::PROPERTY_USE_CODE] == 1)
            {
                $publication->setCode($exportValues[Publication::PROPERTY_CODE]);
            }
            else
            {
                $publication->setCode(null);
            }

            $this->handleFeedbackPeriod($exportValues, $publication);

            return $publication->update();
        }
        catch (\Exception $ex)
        {
            return false;
        }
    }

    /**
     * @param array $values
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\DataClass\Publication|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass $publication
     */
    private function handleFeedbackPeriod($values, $publication)
    {
        if ($values['feedback_period_choice'] == 0)
        {
            $from = $to = 0;
        }
        else if ($values['feedback_period_choice'] == 1)
        {
            $from = DatetimeUtilities::time_from_datepicker($values['feedback_from_date_only']);
            $to = 0;
        }
        else if ($values['feedback_period_choice'] == 2)
        {
            $from = DatetimeUtilities::time_from_datepicker($values['feedback_from_date']);
            $to = DatetimeUtilities::time_from_datepicker($values['feedback_to_date']);
        }
        $publication->setFromDate($from);
        $publication->setToDate($to);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     */
    protected function setDefaultsForPublication(ContentObjectPublication $contentObjectPublication)
    {
        $publication =
            $this->publicationRepository->findPublicationByContentObjectPublication($contentObjectPublication);

        $code = $publication->getCode();
        $defaults = [];
        if (!empty($code))
        {
            $defaults[Publication::PROPERTY_CODE] = $publication->getCode();
            $defaults[self::PROPERTY_USE_CODE] = 1;
        }

        $feedbackFromDate = $publication->getFromDate();
        $feedbackToDate = $publication->getToDate();
        $defaults['feedback_from_date_only'] = $feedbackFromDate;
        $defaults['feedback_from_date'] = $feedbackFromDate;
        $defaults['feedback_to_date'] = $feedbackToDate;

        if ($feedbackFromDate > 0)
        {
            $defaults['feedback_from_date_only'] = $feedbackFromDate;
            $defaults['feedback_from_date'] = $feedbackFromDate;

            if ($feedbackToDate > 0)
            {
                $defaults['feedback_to_date'] = $feedbackToDate;
                $defaults['feedback_period_choice'] = 2;
            }
            else {
                $defaults['feedback_period_choice'] = 1;
            }
        }
        else
        {
            $defaults['feedback_period_choice'] = 0;
        }

        $this->setDefaults($defaults);
    }

    /**
     * @return bool
     */
    protected function isShowTimeWindow()
    {
        return false;
    }
}
