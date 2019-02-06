<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Form;

use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Domain\AssignmentConfiguration;
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
     * ConfigurationForm constructor.
     *
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
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
            null,
            null,
            $this->translator->trans('TypeUsersEntity', [], self::TRANSLATION_CONTEXT),
            Entry::ENTITY_TYPE_USER
        );

        $group[] = $formBuilder->createElement(
            'radio',
            null,
            null,
            $this->translator->trans('TypeCourseGroupsEntity', [], self::TRANSLATION_CONTEXT),
            Entry::ENTITY_TYPE_COURSE_GROUP
        );

        $group[] = $formBuilder->createElement(
            'radio',
            null,
            null,
            $this->translator->trans('TypePlatformGroupsEntity', [], self::TRANSLATION_CONTEXT),
            Entry::ENTITY_TYPE_PLATFORM_GROUP
        );

        $formBuilder->addGroup(
            $group,
            self::FORM_PROPERTY_ENTITY_TYPE,
            $this->translator->trans('AssignmentEntityType', [], self::TRANSLATION_CONTEXT),
            ''
        );

        $formBuilder->addElement(
            'checkbox', self::FORM_PROPERTY_CHECK_FOR_PLAGIARISM,
            $this->translator->trans('CheckForPlagiarism', [], self::TRANSLATION_CONTEXT)
        );

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