<?php
namespace Chamilo\Core\Repository\Quota\Rights\Table\Entity;

use Chamilo\Core\Repository\Quota\Rights\Service\RightsService;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;
use Chamilo\Libraries\Format\Table\Table;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

class EntityTableDataProvider extends RecordTableDataProvider
{
    /**
     * @var \Chamilo\Core\Repository\Quota\Rights\Service\RightsService
     */
    private $rightsService;

    /**
     * @param \Chamilo\Libraries\Format\Table\Table $table
     * @param \Chamilo\Core\Repository\Quota\Rights\Service\RightsService $rightsService
     */
    public function __construct(Table $table, RightsService $rightsService)
    {
        parent::__construct($table);

        $this->rightsService = $rightsService;
    }

    public function countData(?Condition $condition = null): int
    {
        return $this->getRightsService()->countAllRightsLocationEntityRightGroups();
    }

    /**
     * @return \Chamilo\Core\Repository\Quota\Rights\Service\RightsService
     */
    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    /**
     * @param \Chamilo\Core\Repository\Quota\Rights\Service\RightsService $rightsService
     */
    public function setRightsService(RightsService $rightsService): void
    {
        $this->rightsService = $rightsService;
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        return $this->getRightsService()->getRightsLocationEntityRightGroupsWithEntityAndGroup(
            $count, $offset, $orderBy
        );
    }
}
