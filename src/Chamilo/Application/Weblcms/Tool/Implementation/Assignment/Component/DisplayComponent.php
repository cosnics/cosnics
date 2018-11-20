<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Bridge\Assignment\AssignmentServiceBridge;
use Chamilo\Application\Weblcms\Bridge\Assignment\EphorusServiceBridge;
use Chamilo\Application\Weblcms\Bridge\Assignment\FeedbackServiceBridge;
use Chamilo\Application\Weblcms\Bridge\Assignment\NotificationServiceBridge;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\ApplicationFactory;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DisplayComponent extends Manager implements DelegateComponent
{
    /**
     * @return string
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function run()
    {
        $assignmentDataProvider = $this->getAssignmentDataProvider();

        $publication = $this->getContentObjectPublication();
        if (!$this->is_allowed(WeblcmsRights::VIEW_RIGHT, $publication))
        {
            throw new NotAllowedException();
        }

        $breadcrumbTrail = BreadcrumbTrail::getInstance();
        $breadcrumbTrail->add(new Breadcrumb($this->get_url(), $publication->getContentObject()->get_title()));

        $this->buildBridges($publication);

        $assignmentDataProvider->setContentObjectPublication($publication);
        $assignmentDataProvider->setAssignmentPublication($this->getAssignmentPublication($publication));
        $assignmentDataProvider->setCanEditAssignment($this->is_allowed(WeblcmsRights::EDIT_RIGHT, $publication));

        $assignmentDataProvider->setEphorusEnabled($this->isEphorusEnabled());

        $configuration = new ApplicationConfiguration(
            $this->get_application()->getRequest(), $this->get_application()->getUser(), $this
        );

        $configuration->set(
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::CONFIGURATION_DATA_PROVIDER,
            $assignmentDataProvider
        );

        $applicationFactory = $this->getApplicationFactory();
        $applicationFactory->setAssignmentDataProvider($assignmentDataProvider);

        return $applicationFactory->getApplication(
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::context(),
            $configuration
        )->run();

    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function buildBridges(ContentObjectPublication $contentObjectPublication)
    {
        /** @var AssignmentServiceBridge $assignmentServiceBridge */
        $assignmentServiceBridge = $this->getService(AssignmentServiceBridge::class);

        $assignmentServiceBridge->setCanEditAssignment(
            $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $contentObjectPublication)
        );

        $assignmentServiceBridge->setContentObjectPublication($contentObjectPublication);
        $assignmentServiceBridge->setAssignmentPublication($this->getAssignmentPublication($contentObjectPublication));

        /** @var FeedbackServiceBridge $assignmentFeedbackServiceBridge */
        $assignmentFeedbackServiceBridge = $this->getService(FeedbackServiceBridge::class);
        $assignmentFeedbackServiceBridge->setContentObjectPublication($contentObjectPublication);

        /** @var EphorusServiceBridge $assignmentEphorusServiceBridge */
        $assignmentEphorusServiceBridge = $this->getService(EphorusServiceBridge::class);
        $assignmentEphorusServiceBridge->setEphorusEnabled($this->isEphorusEnabled());
        $assignmentEphorusServiceBridge->setContentObjectPublication($contentObjectPublication);

        /** @var NotificationServiceBridge $notificationServiceBridge */
        $notificationServiceBridge = $this->getService(NotificationServiceBridge::class);
        $notificationServiceBridge->setContentObjectPublication($contentObjectPublication);

        $this->getBridgeManager()->addBridge($assignmentServiceBridge);
        $this->getBridgeManager()->addBridge($assignmentEphorusServiceBridge);
        $this->getBridgeManager()->addBridge($assignmentFeedbackServiceBridge);
        $this->getBridgeManager()->addBridge($notificationServiceBridge);
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
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
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\ApplicationFactory
     */
    public function getApplicationFactory()
    {
        return new ApplicationFactory($this->getRequest(), StringUtilities::getInstance(), Translation::getInstance());
    }

    /**
     * @return string[]
     */
    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }
}
