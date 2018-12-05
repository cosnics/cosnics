<?php
namespace Chamilo\Core\Repository\Quota\Rights\Table\Entity;

use Chamilo\Core\Repository\Quota\Rights\Service\RightsService;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;
use Chamilo\Libraries\Format\Table\Table;

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

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function count_data($condition)
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

    public function retrieve_data($condition, $offset, $count, $orderProperties = null)
    {
        return $this->getRightsService()->getRightsLocationEntityRightGroupsWithEntityAndGroup(
            $count, $offset, $orderProperties
        );
    }
}
