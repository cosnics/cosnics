<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentEphorusSupportInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\RightsService;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager implements \Chamilo\Core\Repository\Feedback\FeedbackSupport
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
    const ACTION_DELETE = self::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM;
    const ACTION_AJAX = 'Ajax';
    const ACTION_EPHORUS = 'Ephorus';

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
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
     */
    protected $entry;

    /**
     * @var RightsService
     */
    protected $rightsService;

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
     */
    public function getDataProvider()
    {
        return $this->getApplicationConfiguration()->get(self::CONFIGURATION_DATA_PROVIDER);
    }

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
            $this->entityType = $this->getDataProvider()->getCurrentEntityType();
        }

        return $this->entityType;
    }

    /**
     *
     * @return integer
     */
    public function getEntityIdentifier()
    {
        if (!isset($this->entityIdentifier))
        {
            $this->entityIdentifier = $this->getRequest()->query->get(self::PARAM_ENTITY_ID);

            if (empty($this->entityIdentifier))
            {
                $this->entityIdentifier = $this->getDataProvider()->getCurrentEntityIdentifier($this->getUser());
            }
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
            $this->rightsService->setAssignmentDataProvider($this->getDataProvider());
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

        $this->entry = $this->getDataProvider()->findEntryByIdentifier($entryIdentifier);
    }

    /**
     * @param string[] $parameters
     *
     * @return string[]
     */
    protected function getAvailableEntitiesParameters($parameters)
    {
        $availableEntities = [];

        $availableEntityIds = $this->getDataProvider()->getAvailableEntityIdentifiersForUser($this->getUser());
        foreach ($availableEntityIds as $availableEntityId)
        {
            if ($availableEntityId == $this->getEntityIdentifier())
            {
                continue;
            }

            $availableEntities[$availableEntityId] = $this->getDataProvider()->renderEntityNameByEntityTypeAndEntityId(
                $this->getEntityType(), $availableEntityId
            );
        }

        $parameters['HAS_MULTIPLE_ENTITIES'] = count($availableEntityIds) > 1;
        $parameters['AVAILABLE_ENTITIES'] = $availableEntities;

        $parameters['ENTITY_NAME'] = $this->getDataProvider()->renderEntityNameByEntityTypeAndEntityId(
            $this->getEntityType(), $this->getEntityIdentifier()
        );

        $parameters['ENTITY_TYPE_PLURAL'] =
            strtolower($this->getDataProvider()->getPluralEntityNameByType($this->getEntityType()));

        $parameters['ENTITY_TYPE'] = strtolower($this->getDataProvider()->getEntityNameByType($this->getEntityType()));

        return $parameters;
    }

    /**
     * @return bool
     */
    protected function isEphorusEnabled()
    {
        $dataProvider = $this->getDataProvider();

        if(!$dataProvider instanceof AssignmentEphorusSupportInterface)
        {
            return false;
        }

        return $dataProvider->isEphorusEnabled();
    }

    public function get_content_object_display_attachment_url($attachment,
                                                              $selected_complex_content_object_item_id = null)
    {
        $parameters = [
                static::PARAM_ACTION => self::ACTION_VIEW_ATTACHMENT,
                self::PARAM_ATTACHMENT_ID => $attachment->get_id(),
                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item_id
        ];

        if($this->getEntry() instanceof Entry)
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
        return $this->getDataProvider()->findFeedbackByEntry($this->getEntry());
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::count_feedbacks()
     */
    public function count_feedbacks()
    {
        return $this->getDataProvider()->countFeedbackByEntry($this->getEntry());
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::retrieve_feedback()
     */
    public function retrieve_feedback($feedbackIdentifier)
    {
        return $this->getDataProvider()->findFeedbackByIdentifier($feedbackIdentifier);
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::get_feedback()
     */
    public function get_feedback()
    {
        $feedback = $this->getDataProvider()->initializeFeedback();
        $feedback->setEntryId($this->getEntry()->getId());

        return $feedback;
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
}
