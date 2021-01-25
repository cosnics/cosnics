<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Form;

use Chamilo\Application\Weblcms\Form\ContentObjectPublicationForm;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\DataClass\Publication;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\Repository\PublicationRepository;
use Chamilo\Configuration\Service\RegistrationConsulter;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Translation\Translation;
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

        $group = [];

        $group[] = $this->createElement(
            'radio', self::PROPERTY_USE_CODE, '', $this->translator->trans('NoCode', [], Manager::context()), 0,
            array('id' => 'no_code')
        );

        $group[] = $this->createElement(
            'radio', self::PROPERTY_USE_CODE, '', $this->translator->trans('UseCode', [], Manager::context()), 1,
            array('id' => 'use_code')
        );

        $group[] = $this->create_textfield(
            Publication::PROPERTY_CODE, $this->translator->trans('AccessCode', [], Manager::context()),
            [
                'type' => 'numeric', 'min' => 10000, 'max' => 99999, 'maxlength' => 5, 'minlength' => 5,
                'id' => 'access_code'
            ]
        );

        $this->addGroup($group, null, Translation::get('AccessCode'), '', false);

        $javascript = [];
        $javascript[] = '<script type="text/javascript">';
        $javascript[] = '$(document).ready(function() {';
        $javascript[] = '   $("#use_code").on("click", function() {';
        $javascript[] = '       $("#access_code").show();';
        $javascript[] = '       $("#access_code").attr("required", "required");';
        $javascript[] = '   });';
        $javascript[] = '   $("#no_code").on("click", function() {';
        $javascript[] = '       $("#access_code").hide();';
        $javascript[] = '       $("#access_code").removeAttr("required");';
        $javascript[] = '   });';
        $javascript[] = '   if($("#no_code").attr("checked") == "checked") {';
        $javascript[] = '      $("#access_code").hide();';
        $javascript[] = '   };';
        $javascript[] = '});';
        $javascript[] = '</script>';

        $this->addElement('html', implode(PHP_EOL, $javascript));
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

            return $publication->update();
        }
        catch (\Exception $ex)
        {
            return false;
        }
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     */
    protected function setDefaultsForPublication(ContentObjectPublication $contentObjectPublication)
    {
        $publication =
            $this->publicationRepository->findPublicationByContentObjectPublication($contentObjectPublication);

        $code = $publication->getCode();
        if (!empty($code))
        {
            $defaults = [Publication::PROPERTY_CODE => $publication->getCode(), self::PROPERTY_USE_CODE => 1];
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
