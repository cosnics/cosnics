<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository\AssignmentRepository;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\AssignmentDataProvider;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DisplayComponent extends Manager
{
    public function run()
    {
        $assignmentDataProvider = new AssignmentDataProvider(
            $this->get_application()->getTranslator(),
            new AssignmentService(new AssignmentRepository($this->getDataClassRepository()))
        );

        $publication = $this->getContentObjectPublication();

        $assignmentDataProvider->setContentObjectPublication($publication);

        $targetUsers = DataManager::get_publication_target_users_by_publication_id(
            $publication->getId()
        );

        $targetUserIds = [];

        foreach($targetUsers as $targetUser)
        {
            $targetUserIds[] = $targetUser instanceof User ? $targetUser->getId() : $targetUser[User::PROPERTY_ID];
        }

        $assignmentDataProvider->setTargetUserIds($targetUserIds);
        $assignmentDataProvider->setCanEditAssignment($this->is_allowed(WeblcmsRights::EDIT_RIGHT, $publication));

        $configuration = new ApplicationConfiguration(
            $this->get_application()->getRequest(), $this->get_application()->getUser(), $this
        );

        $configuration->set(
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::CONFIGURATION_DATA_PROVIDER,
            $assignmentDataProvider
        );

        $applicationFactory = $this->getApplicationFactory();

        return $applicationFactory->getApplication(
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::context(),
            $configuration
        )->run();
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function get_root_content_object()
    {
        return $this->getContentObjectPublication()->getContentObject();
    }

    /**
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function getContentObjectPublication()
    {
        $contentObjectPublicationId =
            $this->getRequest()->getFromUrl(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);

        $contentObjectPublicationTranslation =
            $this->getTranslator()->trans('ContentObjectPublication', [], Manager::context());

        if (empty($contentObjectPublicationId))
        {
            throw new NoObjectSelectedException($contentObjectPublicationTranslation);
        }

        $contentObjectPublication =
            DataManager::retrieve_by_id(ContentObjectPublication::class_name(), $contentObjectPublicationId);

        if (!$contentObjectPublication instanceof ContentObjectPublication)
        {
            throw new ObjectNotExistException($contentObjectPublicationTranslation, $contentObjectPublicationId);
        }

        return $contentObjectPublication;
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected function getDataClassRepository()
    {
        return $this->get_application()->getService(
            'chamilo.libraries.storage.data_manager.doctrine.data_class_repository'
        );
    }

    /**
     * @return string[]
     */
    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }
}