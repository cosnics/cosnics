<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Component;

use Chamilo\Application\Weblcms\Bridge\Assignment\AssignmentServiceBridge;
use Chamilo\Application\Weblcms\Bridge\Assignment\EntryPlagiarismResultServiceBridge;
use Chamilo\Application\Weblcms\Bridge\Assignment\EphorusServiceBridge;
use Chamilo\Application\Weblcms\Bridge\Assignment\FeedbackServiceBridge;
use Chamilo\Application\Weblcms\Bridge\Assignment\NotificationServiceBridge;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\ApplicationFactory;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface;
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
 * @package Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Component
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
        $publication = $this->getContentObjectPublication();
        if (!$this->is_allowed(WeblcmsRights::VIEW_RIGHT, $publication))
        {
            throw new NotAllowedException();
        }

        $breadcrumbTrail = BreadcrumbTrail::getInstance();
        $breadcrumbTrail->add(new Breadcrumb($this->get_url(), $publication->getContentObject()->get_title()));

        $this->buildBridges($publication);

        $configuration = new ApplicationConfiguration(
            $this->get_application()->getRequest(), $this->getUser(), $this
        );

        $applicationFactory = $this->getApplicationFactory();
        $applicationFactory->setAssignmentServiceBridge(
            $this->getBridgeManager()->getBridgeByInterface(AssignmentServiceBridgeInterface::class)
        );

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

        $assignmentPublication = $this->getAssignmentPublication($contentObjectPublication);

        $assignmentServiceBridge->setContentObjectPublication($contentObjectPublication);
        $assignmentServiceBridge->setAssignmentPublication($assignmentPublication);

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

        /** @var \Chamilo\Application\Weblcms\Bridge\Assignment\EntryPlagiarismResultServiceBridge $entryPlagiarismResultServiceBridge */
        $entryPlagiarismResultServiceBridge = $this->getService(EntryPlagiarismResultServiceBridge::class);
        $entryPlagiarismResultServiceBridge->setContentObjectPublication($contentObjectPublication);
        $entryPlagiarismResultServiceBridge->setAssignmentPublication($assignmentPublication);

        $this->getBridgeManager()->addBridge($assignmentServiceBridge);
        $this->getBridgeManager()->addBridge($assignmentEphorusServiceBridge);
        $this->getBridgeManager()->addBridge($assignmentFeedbackServiceBridge);
        $this->getBridgeManager()->addBridge($notificationServiceBridge);
        $this->getBridgeManager()->addBridge($entryPlagiarismResultServiceBridge);
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
