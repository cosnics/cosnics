<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Repository\EntityRepository;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class EntityService
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Repository\EntityRepository
     */
    protected $entityRepository;

    public function __construct(EntityRepository $entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }

    /**
     *
     * @param int[] $userIds
     * @param FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function getUsersFromIDs(array $userIds, FilterParameters $filterParameters)
    {
        return $this->entityRepository->getUsersFromIDs($userIds, $filterParameters);
    }

    /**
     *
     * @param int[] $userIds
     * @param FilterParameters $filterParameters
     *
     * @return integer
     */
    public function countUsersFromIDs(array $userIds, FilterParameters $filterParameters)
    {
        return $this->entityRepository->countUsersFromIDs($userIds, $filterParameters);
    }
}