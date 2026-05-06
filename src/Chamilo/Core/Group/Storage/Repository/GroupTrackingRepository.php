<?php
namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Storage\DataClass\GroupActivity;
use Chamilo\Libraries\Storage\Repository\DataClassRepository;

/**
 * @package Chamilo\Core\Group\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupTrackingRepository
{
    public function __construct(protected DataClassRepository $dataClassRepository)
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function createGroupActivity(GroupActivity $groupActivity): bool
    {
        return $this->dataClassRepository->create($groupActivity);
    }
}