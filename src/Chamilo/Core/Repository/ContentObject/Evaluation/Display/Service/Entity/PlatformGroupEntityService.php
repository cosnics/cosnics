<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Storage\Repository\EntityRepository;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\FilterParameters\FieldMapper;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class PlatformGroupEntityService implements EvaluationEntityServiceInterface
{
    /**
     * @var EntityRepository
     */
    protected $entityRepository;

    public function __construct(EntityRepository $entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }

    /**
     *
     * @param int[] $entityIds
     * @param ContextIdentifier $contextIdentifier
     * @param FilterParameters|null $filterParameters
     *
     * @return RecordIterator
     */
    public function getEntitiesFromIds(array $entityIds, ContextIdentifier $contextIdentifier, FilterParameters $filterParameters = null): RecordIterator
    {
        if (is_null($filterParameters))
        {
            $filterParameters = new FilterParameters();
        }

        return $this->entityRepository->getPlatformGroupsFromIds($entityIds, $contextIdentifier, $filterParameters);
    }

    /**
     *
     * @param int[] $entityIds
     * @param FilterParameters $filterParameters
     *
     * @return integer
     */
    public function countEntitiesFromIds(array $entityIds, FilterParameters $filterParameters): int
    {
        return $this->entityRepository->countPlatformGroupsFromIds($entityIds, $filterParameters);
    }

    /**
     * @return FieldMapper
     */
    public function getFieldMapper(): FieldMapper
    {
        if (! isset($this->fieldMapper))
        {
            $this->fieldMapper = new FieldMapper();
            $this->fieldMapper->addFieldMapping('name', Group::class_name(), Group::PROPERTY_NAME);
        }
        return $this->fieldMapper;
    }
}
