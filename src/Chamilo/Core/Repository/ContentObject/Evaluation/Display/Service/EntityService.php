<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Repository\EntityRepository;

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

    public function getUsersFromIds(array $userIds, array $sortProperties, $sortColumn = null, bool $sortDesc = false, $offset = null, $count = null)
    {
        return $this->entityRepository->getUsersFromIds($userIds, $sortProperties, $sortColumn, $sortDesc, $offset, $count);
    }
}