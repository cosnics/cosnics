<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Form;

use Chamilo\Application\Weblcms\Form\ContentObjectPublicationForm;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\DataClass\Publication;
use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\Repository\PublicationRepository;
use Chamilo\Application\Weblcms\Bridge\Evaluation\Domain\EntityTypes;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Form
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationForm extends ContentObjectPublicationForm
{
    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\Repository\PublicationRepository
     */
    protected $publicationRepository;

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
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\Repository\PublicationRepository $publicationRepository
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     */
    public function __construct(
        User $user, $form_type, $publications, $course, $action, $is_course_admin,
        $selectedContentObjects = array(), Translator $translator, PublicationRepository $publicationRepository
    )
    {
        $this->translator = $translator;
        $this->publicationRepository = $publicationRepository;

        parent::__construct(
            'Chamilo\Application\Weblcms\Tool\Implementation\Evaluation',
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
        $this->addEvaluationProperties();
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
        $this->addEvaluationProperties();
    }

    /**
     * @throws \HTML_QuickForm_Error
     * @throws \PEAR_Error
     */
    protected function addEvaluationProperties()
    {
        $this->addElement('category', $this->translator->trans('EvaluationProperties', [], Manager::context()));

        $group[] = $this->createElement(
            'radio',
            null,
            null,
            $this->translator->trans('TypeUsersEntity', [], Manager::context()),
            EntityTypes::ENTITY_TYPE_USER()->getValue()
        );

        $group[] = $this->createElement(
            'radio',
            null,
            null,
            $this->translator->trans('TypeCourseGroupsEntity', [], Manager::context()),
            EntityTypes::ENTITY_TYPE_COURSE_GROUP()->getValue()
        );

        $group[] = $this->createElement(
            'radio',
            null,
            null,
            $this->translator->trans('TypePlatformGroupsEntity', [], Manager::context()),
            EntityTypes::ENTITY_TYPE_PLATFORM_GROUP()->getValue()
        );

        $this->addGroup(
            $group,
            Publication::PROPERTY_ENTITY_TYPE,
            $this->translator->trans('PublishEvaluationForEntity', [], Manager::context()),
            ''
        );

        $this->addElement(
            'checkbox', Publication::PROPERTY_OPEN_FOR_STUDENTS,
            $this->translator->trans('OpenForStudents', [], Manager::context())
        );

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
        $publication->setEntityType($exportValues[Publication::PROPERTY_ENTITY_TYPE]);
        $publication->setOpenForStudents($exportValues[Publication::PROPERTY_OPEN_FOR_STUDENTS] == 1);

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

            $publication->setEntityType($exportValues[Publication::PROPERTY_ENTITY_TYPE]);
            $publication->setOpenForStudents($exportValues[Publication::PROPERTY_OPEN_FOR_STUDENTS] == 1);

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

        $this->setDefaults([Publication::PROPERTY_ENTITY_TYPE => $publication->getEntityType()]);
        $this->setDefaults([Publication::PROPERTY_OPEN_FOR_STUDENTS => $publication->getOpenForStudents()]);
    }
}