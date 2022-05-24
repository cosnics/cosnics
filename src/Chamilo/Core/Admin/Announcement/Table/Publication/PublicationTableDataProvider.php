<?php
namespace Chamilo\Core\Admin\Announcement\Table\Publication;

use Chamilo\Core\Admin\Announcement\Component\BrowserComponent;
use Chamilo\Core\Admin\Announcement\Service\PublicationService;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

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

    public function countData(?Condition $condition = null): int
    {
        $type = $this->get_component()->getType();
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

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        $type = $this->get_component()->getType();
        switch ($type)
        {
            case BrowserComponent::TYPE_FROM_ME :
            case BrowserComponent::TYPE_ALL :
                return $this->getPublicationService()->findPublicationRecords(
                    $condition, $count, $offset, $orderBy
                );
                break;
            default :
                return $this->getPublicationService()->findVisiblePublicationRecordsForUserIdentifier(
                    $this->get_component()->getUser()->getId(), $condition, $count, $offset, $orderBy
                );
                break;
        }
    }
}
