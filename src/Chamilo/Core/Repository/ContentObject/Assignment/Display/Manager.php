<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\EntryPlagiarismResultServiceBridge;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\EphorusServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\FeedbackServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\NotificationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\Extensions\ExtensionManager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\FeedbackRightsServiceBridge;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\FeedbackServiceBridge;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
    implements \Chamilo\Core\Repository\Feedback\FeedbackSupport
{
    const PARAM_ACTION = 'assignment_display_action';

    // Configuration
    const CONFIGURATION_DATA_PROVIDER = 'data_provider';

    // Parameters
    const PARAM_ENTITY_TYPE = 'entity_type';
    const PARAM_ENTITY_ID = 'entity_id';
    const PARAM_ENTRY_ID = 'entry_id';

    // Actions
    const ACTION_CREATE = self::ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM;
    const ACTION_CREATE_CONFIRMATION = 'CreatorConfirmation';
    const ACTION_VIEW = self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;
    const ACTION_DOWNLOAD = 'Downloader';
    const ACTION_SUBMIT = 'Submitter';
    const ACTION_BROWSE = 'Browser';
    const ACTION_ENTRY = 'Entry';
    const ACTION_ENTRY_CODE_PAGE_CORRECTOR = 'EntryCodePageCorrector';
    const ACTION_DELETE = self::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM;
    const ACTION_AJAX = 'Ajax';
    const ACTION_EPHORUS = 'Ephorus';
    const ACTION_EXTENSION = 'Extension';

    /**
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);
        $this->buildBridgeServices();
    }

    /**
     * Builds the bridge services for the feedback and for the extensions
     */
    protected function buildBridgeServices()
    {
        /** @var FeedbackServiceBridgeInterface $assignmentFeedbackServiceBridge */
        $assignmentFeedbackServiceBridge =
            $this->getBridgeManager()->getBridgeByInterface(FeedbackServiceBridgeInterface::class);

        /** @var NotificationServiceBridgeInterface $notificationServiceBridge */
        $notificationServiceBridge =
            $this->getBridgeManager()->getBridgeByInterface(NotificationServiceBridgeInterface::class);

        $feedbackServiceBridge =
            new FeedbackServiceBridge($assignmentFeedbackServiceBridge, $notificationServiceBridge);

        if($this->getEntry() instanceof Entry)
        {
            $feedbackServiceBridge->setEntry($this->getEntry());
        }

        $feedbackRightsServiceBridge = new FeedbackRightsServiceBridge();
        $feedbackRightsServiceBridge->setCurrentUser($this->getUser());

        $assignmentEntryPlagiarismResultServiceBridge =
            $this->getBridgeManager()->getBridgeByInterface(EntryPlagiarismResultServiceBridgeInterface::class);

        $entryPlagiarismResultServiceBridge =
            new EntryPlagiarismResultServiceBridge($assignmentEntryPlagiarismResultServiceBridge);

        $this->getBridgeManager()->addBridge($feedbackServiceBridge);
        $this->getBridgeManager()->addBridge($feedbackRightsServiceBridge);
        $this->getBridgeManager()->addBridge($entryPlagiarismResultServiceBridge);
    }

    /**
     *
     * @var integer
     */
    protected $entityType;

    /**
     *
     * @var integer
     */
    protected $entityIdentifier;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
    protected $entry;

    /**
     * @var RightsService
     */
    protected $rightsService;

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject | \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment
     */
    public function getAssignment()
    {
        return $this->get_root_content_object();
    }

    /**
     *
     * @return integer
     */
    public function getEntityType()
    {
        if (!isset($this->entityType))
        {
            $this->entityType = $this->getAssignmentServiceBridge()->getCurrentEntityType();
        }

        return $this->entityType;
    }

    /**
     *
     * @return integer
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function getEntityIdentifier()
    {
        if (!isset($this->entityIdentifier))
        {
            $this->entityIdentifier = $this->getRequest()->query->get(self::PARAM_ENTITY_ID);

            if (empty($this->entityIdentifier))
            {
                $this->entityIdentifier =
                    $this->getAssignmentServiceBridge()->getCurrentEntityIdentifier($this->getUser());
            }
        }

        if (empty($this->entityIdentifier))
        {
            throw new UserException($this->getTranslator()->trans('CanNotViewAssignment', [], Manager::context()));
        }

        return $this->entityIdentifier;
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\RightsService
     */
    public function getRightsService()
    {
        if (!isset($this->rightsService))
        {
            $this->rightsService = new RightsService();
            $this->rightsService->setAssignmentServiceBridge($this->getAssignmentServiceBridge());
        }

        return $this->rightsService;
    }

    public function getEntry()
    {
        if (!isset($this->entry))
        {
            try
            {
                $this->initializeEntry();
            }
            catch (\Exception $ex)
            {
            }
        }

        return $this->entry;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     */
    protected function initializeEntry()
    {
        $entryIdentifier = $this->getRequest()->query->get(self::PARAM_ENTRY_ID);
        if (!$entryIdentifier)
        {
            throw new NoObjectSelectedException(Translation::get('Entry'));
        }
        else
        {
            $this->set_parameter(self::PARAM_ENTRY_ID, $entryIdentifier);
        }

        $this->entry = $this->getAssignmentServiceBridge()->findEntryByIdentifier($entryIdentifier);
    }

    /**
     * @param string[] $parameters
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    protected function getAvailableEntitiesParameters($parameters)
    {
        $availableEntities = [];

        $availableEntityIds =
            $this->getAssignmentServiceBridge()->getAvailableEntityIdentifiersForUser($this->getUser());
        foreach ($availableEntityIds as $availableEntityId)
        {
            if ($availableEntityId == $this->getEntityIdentifier())
            {
                continue;
            }

            $availableEntities[$availableEntityId] =
                $this->getAssignmentServiceBridge()->renderEntityNameByEntityTypeAndEntityId(
                    $this->getEntityType(), $availableEntityId
                );
        }

        $parameters['HAS_MULTIPLE_ENTITIES'] = count($availableEntityIds) > 1;
        $parameters['AVAILABLE_ENTITIES'] = $availableEntities;

        $parameters['ENTITY_NAME'] = $this->getAssignmentServiceBridge()->renderEntityNameByEntityTypeAndEntityId(
            $this->getEntityType(), $this->getEntityIdentifier()
        );

        $parameters['ENTITY_TYPE_PLURAL'] =
            strtolower($this->getAssignmentServiceBridge()->getPluralEntityNameByType($this->getEntityType()));

        $parameters['ENTITY_TYPE'] =
            strtolower($this->getAssignmentServiceBridge()->getEntityNameByType($this->getEntityType()));

        return $parameters;
    }

    /**
     * @return bool
     */
    protected function isEphorusEnabled()
    {
        return $this->getEphorusServiceBridge()->isEphorusEnabled();
    }

    public function get_content_object_display_attachment_url(
        $attachment,
        $selected_complex_content_object_item_id = null
    )
    {
        $parameters = [
            static::PARAM_ACTION => self::ACTION_VIEW_ATTACHMENT,
            self::PARAM_ATTACHMENT_ID => $attachment->get_id(),
            self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item_id
        ];

        if ($this->getEntry() instanceof Entry)
        {
            $parameters[self::PARAM_ENTRY_ID] = $this->getEntry()->getId();
        }

        return $this->get_url($parameters);
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::retrieve_feedbacks()
     */
    public function retrieve_feedbacks($count, $offset)
    {
        return $this->getFeedbackServiceBridge()->getFeedbackByEntry($this->getEntry());
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::count_feedbacks()
     */
    public function count_feedbacks()
    {
        return $this->getFeedbackServiceBridge()->countFeedbackByEntry($this->getEntry());
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::retrieve_feedback()
     */
    public function retrieve_feedback($feedbackIdentifier)
    {
        return $this->getFeedbackServiceBridge()->getFeedbackByIdentifier($feedbackIdentifier);
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::get_feedback()
     */
    public function get_feedback()
    {
        return null;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::is_allowed_to_view_feedback()
     */
    public function is_allowed_to_view_feedback()
    {
        return true;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::is_allowed_to_create_feedback()
     */
    public function is_allowed_to_create_feedback()
    {
        return true;
    }

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback $feedback
     *
     * @return bool
     */
    public function is_allowed_to_update_feedback($feedback)
    {
        return $feedback->get_user_id() == $this->getUser()->getId();
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::is_allowed_to_delete_feedback()
     */
    public function is_allowed_to_delete_feedback($feedback)
    {
        return $feedback->get_user_id() == $this->getUser()->getId();
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\NotificationServiceBridgeInterface
     */
    protected function getNotificationServiceBridge()
    {
        return $this->getBridgeManager()->getBridgeByInterface(NotificationServiceBridgeInterface::class);
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface
     */
    protected function getAssignmentServiceBridge()
    {
        return $this->getBridgeManager()->getBridgeByInterface(AssignmentServiceBridgeInterface::class);
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\FeedbackServiceBridgeInterface
     */
    protected function getFeedbackServiceBridge()
    {
        return $this->getBridgeManager()->getBridgeByInterface(FeedbackServiceBridgeInterface::class);
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\EphorusServiceBridgeInterface
     */
    protected function getEphorusServiceBridge()
    {
        return $this->getBridgeManager()->getBridgeByInterface(EphorusServiceBridgeInterface::class);
    }

    /**
     * @return ExtensionManager
     */
    protected function getExtensionManager()
    {
        return $this->getService(ExtensionManager::class);
    }

    /**
     * @return ContentObjectRepository
     */
    protected function getContentObjectRepository()
    {
        return $this->getService(ContentObjectRepository::class);
    }
}
