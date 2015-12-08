<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    // Configuration
    const CONFIGURATION_DATA_PROVIDER = 'data_provider';

    // Parameters
    const PARAM_ENTITY_TYPE = 'entity_type';
    const PARAM_ENTITY_ID = 'entity_id';

    // Actions
    const ACTION_VIEW = 'Viewer';
    const ACTION_DOWNLOAD = 'Downloader';
    const ACTION_SUBMIT = 'Submitter';

    /**
     *
     * @var integer
     */
    private $entityType;

    /**
     *
     * @var integer
     */
    private $entityIdentifier;

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
     */
    public function getDataProvider()
    {
        return $this->getApplicationConfiguration()->get(self :: CONFIGURATION_DATA_PROVIDER);
    }

    /**
     *
     * @return integer
     */
    public function getEntityType()
    {
        if (! isset($this->entityType))
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
        if (! isset($this->entityIdentifier))
        {
            $this->entityIdentifier = $this->getRequest()->query->get(self :: PARAM_ENTITY_ID);
        }

        return $this->entityIdentifier;
    }
}
