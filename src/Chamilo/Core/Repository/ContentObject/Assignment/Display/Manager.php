<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
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

            if(empty($this->entityIdentifier))
            {
                $this->entityIdentifier = $this->getDataProvider()->getCurrentEntityIdentifier($this->getUser());
            }
        }

        return $this->entityIdentifier;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return bool
     */
    public function canUserAccessEntry(Entry $entry)
    {
        return $this->canUserAccessEntity($entry->getEntityType(), $entry->getEntityId());
    }

    /**
     * @param int $entityType
     * @param int $entityId
     *
     * @return bool
     */
    public function canUserAccessEntity($entityType, $entityId)
    {
        if($this->getDataProvider()->canEditAssignment())
        {
            return true;
        }

        return $this->getDataProvider()->isUserPartOfEntity($this->getUser(), $entityType, $entityId);
    }

    public function getEntry()
    {
        if(!isset($this->entry))
        {
            try
            {
                $this->initializeEntry();
            }
            catch(\Exception $ex)
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
}
