<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Form;

use Chamilo\Application\Plagiarism\Service\Turnitin\EulaService;
use Chamilo\Configuration\Service\RegistrationConsulter;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Domain\AssignmentConfiguration;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Utilities\Utilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Form
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ConfigurationFormBuilder
{
    const FORM_PROPERTY_ENTITY_TYPE = 'entity_type';
    const FORM_PROPERTY_CHECK_FOR_PLAGIARISM = 'check_for_plagiarism';

    const TRANSLATION_CONTEXT = 'Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath';

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @var \Chamilo\Configuration\Service\RegistrationConsulter
     */
    protected $registrationConsulter;

    /**
     * ConfigurationForm constructor.
     *
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Configuration\Service\RegistrationConsulter $registrationConsulter
     */
    public function __construct(Translator $translator, RegistrationConsulter $registrationConsulter)
    {
        $this->translator = $translator;
        $this->registrationConsulter = $registrationConsulter;
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formBuilder
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Domain\AssignmentConfiguration $configuration
     *
     * @throws \HTML_QuickForm_Error
     * @throws \PEAR_Error
     * @throws \Exception
     */
    public function buildForm(FormValidator $formBuilder, AssignmentConfiguration $configuration)
    {
        $group[] = $formBuilder->createElement(
            'radio',
            self::FORM_PROPERTY_ENTITY_TYPE,
            null,
            $this->translator->trans('TypeUsersEntity', [], self::TRANSLATION_CONTEXT),
            Entry::ENTITY_TYPE_USER
        );

        $group[] = $formBuilder->createElement(
            'radio',
            self::FORM_PROPERTY_ENTITY_TYPE,
            null,
            $this->translator->trans('TypeCourseGroupsEntity', [], self::TRANSLATION_CONTEXT),
            Entry::ENTITY_TYPE_COURSE_GROUP
        );

        $group[] = $formBuilder->createElement(
            'radio',
            self::FORM_PROPERTY_ENTITY_TYPE,
            null,
            $this->translator->trans('TypePlatformGroupsEntity', [], self::TRANSLATION_CONTEXT),
            Entry::ENTITY_TYPE_PLATFORM_GROUP
        );

        $formBuilder->addGroup(
            $group,
            null,
            $this->translator->trans('AssignmentEntityType', [], self::TRANSLATION_CONTEXT),
            ''
        );

        if($this->registrationConsulter->isContextRegisteredAndActive('Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism'))
        {
            $redirect = new Redirect(
                [
                    Application::PARAM_CONTEXT => 'Chamilo\Application\Plagiarism',
                    Application::PARAM_ACTION => 'TurnitinEula',
                    'ViewOnly' => 1
                ]
            );

            $eulaPageUrl = $redirect->getUrl();

            $formBuilder->addElement(
                'checkbox', self::FORM_PROPERTY_CHECK_FOR_PLAGIARISM,
                $this->translator->trans('CheckForPlagiarism', [], 'Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism')
            );

            $formBuilder->addElement(
                'html', '<div class="alert alert-info" style="margin-top: 15px;">' .
                $this->translator->trans('CheckForPlagiarismEULAWarning', ['{EULA_PAGE_URL}' => $eulaPageUrl], 'Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism') .
                '</div>'
            );
        }
        else
        {
            $formBuilder->addElement('hidden', self::FORM_PROPERTY_CHECK_FOR_PLAGIARISM);
        }

        $buttons = array();

        $buttons[] = $formBuilder->createElement(
            'style_submit_button',
            'submit',
            $this->translator->trans('Save', [], Utilities::COMMON_LIBRARIES),
            null,
            null,
            'arrow-right'
        );

        $formBuilder->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $formBuilder->setDefaults(
            [
                self::FORM_PROPERTY_ENTITY_TYPE => $configuration->getEntityType(),
                self::FORM_PROPERTY_CHECK_FOR_PLAGIARISM => $configuration->getCheckForPlagiarism()
            ]
        );
    }

}