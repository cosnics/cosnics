<?php
namespace Chamilo\Core\Admin\Announcement\Table\Publication;

use Chamilo\Core\Admin\Announcement\Component\BrowserComponent;
use Chamilo\Core\Admin\Announcement\Service\PublicationService;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

class PublicationTableDataProvider extends RecordTableDataProvider
{
    /**
     * @var \Chamilo\Core\Admin\Announcement\Service\PublicationService
     */
    private $publicationService;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Format\Table\Table $table
     * @param \Chamilo\Core\Admin\Announcement\Service\PublicationService $publicationService
     */
    public function __construct($table, PublicationService $publicationService)
    {
        parent::__construct($table);
        $this->publicationService = $publicationService;
    }

    /**
     * @return \Chamilo\Core\Admin\Announcement\Service\PublicationService
     */
    public function getPublicationService(): PublicationService
    {
        return $this->publicationService;
    }

    /**
     * @param \Chamilo\Core\Admin\Announcement\Service\PublicationService $publicationService
     */
    public function setPublicationService(PublicationService $publicationService): void
    {
        $this->publicationService = $publicationService;
    }

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $type = $this->get_component()->get_type();
        switch ($type)
        {
            case BrowserComponent::TYPE_FROM_ME :
            case BrowserComponent::TYPE_ALL :
                return $this->getPublicationService()->findPublicationRecords(
                    $condition, $count, $offset, $order_property
                );
                break;
            default :
                return $this->getPublicationService()->findVisiblePublicationRecordsForUserIdentifier(
                    $this->get_component()->getUser()->getId(), $condition, $count, $offset, $order_property
                );
                break;
        }
    }

    public function count_data($condition)
    {
        $type = $this->get_component()->get_type();
        switch ($type)
        {
            case BrowserComponent::TYPE_FROM_ME :
            case BrowserComponent::TYPE_ALL :
                return $this->getPublicationService()->countPublications($condition);
                break;
            default :
                return $this->getPublicationService()->countVisiblePublicationsForUserIdentifier(
                    $this->get_component()->getUser()->getId(), $condition
                );
                break;
        }
    }
}
