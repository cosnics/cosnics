<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Storage\Repository\EntityRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\FilterParameters\FieldMapper;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class UserEntityService implements EvaluationEntityServiceInterface
{
    /**
     * @var EntityRepository
     */
    protected $entityRepository;

    /**
     * @var FieldMapper
     */
    protected $fieldMapper;

    public function __construct(EntityRepository $entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }

    /**
     *
     * @param int[] $entityIds
     * @param ContextIdentifier $contextIdentifier
     * @param EvaluationEntityRetrieveProperties $evaluationEntityRetrieveProperties
     * @param FilterParameters|null $filterParameters
     *
     * @return RecordIterator
     */
    public function getEntitiesFromIds(array $entityIds, ContextIdentifier $contextIdentifier, EvaluationEntityRetrieveProperties $evaluationEntityRetrieveProperties, FilterParameters $filterParameters = null): RecordIterator
    {
        if (is_null($filterParameters))
        {
            $filterParameters = new FilterParameters();
        }

        return $this->entityRepository->getUsersFromIds($entityIds, $contextIdentifier, $evaluationEntityRetrieveProperties, $filterParameters);
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
        return $this->entityRepository->countUsersFromIds($entityIds, $filterParameters);
    }

    /**
     * @return FieldMapper
     */
    public function getFieldMapper(): FieldMapper
    {
        if (! isset($this->fieldMapper))
        {
            $class_name = User::class_name();
            $this->fieldMapper = new FieldMapper();
            $this->fieldMapper->addFieldMapping('firstname', $class_name, User::PROPERTY_FIRSTNAME);
            $this->fieldMapper->addFieldMapping('lastname', $class_name, User::PROPERTY_LASTNAME);
            $this->fieldMapper->addFieldMapping('official_code', $class_name, User::PROPERTY_OFFICIAL_CODE);
        }
        return $this->fieldMapper;
    }
}