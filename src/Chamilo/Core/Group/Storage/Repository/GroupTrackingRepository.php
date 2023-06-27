<?php
namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Storage\DataClass\GroupActivity;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;

/**
 * @package Chamilo\Core\Group\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupTrackingRepository
{
    private DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    public function createGroupActivity(GroupActivity $groupActivity): bool
    {
        return $this->getDataClassRepository()->create($groupActivity);
    }

    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

}