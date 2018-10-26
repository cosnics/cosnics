<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Form;

use Chamilo\Application\Weblcms\Form\ContentObjectPublicationForm;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Translation\Translation;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Form
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
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository
     */
    private $publicationRepository;

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
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository $publicationRepository
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function __construct(
        User $user, $form_type, $publications, $course, $action, $is_course_admin,
        $selectedContentObjects = array(), Translator $translator, PublicationRepository $publicationRepository
    )
    {
        $this->translator = $translator;
        $this->publicationRepository = $publicationRepository;

        parent::__construct(
            'Chamilo\Application\Weblcms\Tool\Implementation\Assignment',
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
        $this->addAssignmentProperties();
    }

    /**
     * @throws \HTML_QuickForm_Error
     * @throws \PEAR_Error
     */
    protected function addAssignmentProperties()
    {
        $this->addElement('category', $this->translator->trans('AssignmentProperties', [], Manager::context()));

        $group[] = $this->createElement(
            'radio',
            null,
            null,
            $this->translator->trans('TypeUsersEntity', [], Manager::context()),
            Entry::ENTITY_TYPE_USER
        );

        $group[] = $this->createElement(
            'radio',
            null,
            null,
            $this->translator->trans('TypeCourseGroupsEntity', [], Manager::context()),
            Entry::ENTITY_TYPE_COURSE_GROUP
        );

        $group[] = $this->createElement(
            'radio',
            null,
            null,
            $this->translator->trans('TypePlatformGroupsEntity', [], Manager::context()),
            Entry::ENTITY_TYPE_PLATFORM_GROUP
        );

        $this->addGroup(
            $group,
            Publication::PROPERTY_ENTITY_TYPE,
            $this->translator->trans('PublishAssignmentForEntity', [], Manager::context()),
            ''
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
    }
}