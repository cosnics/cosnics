<?php
namespace Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityRetrieveProperties;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityServiceInterface;
use Chamilo\Application\Weblcms\Bridge\Evaluation\Storage\Repository\EntityRepository;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\FilterParameters\FieldMapper;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class CourseGroupEntityService implements EvaluationEntityServiceInterface
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
        return $this->entityRepository->getGroupsFromIds($entityIds, $contextIdentifier, $evaluationEntityRetrieveProperties, $filterParameters);
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
        return $this->entityRepository->countGroupsFromIds($entityIds, $filterParameters);
    }

    /**
     * @return FieldMapper
     */
    public function getFieldMapper(): FieldMapper
    {
        if (! isset($this->fieldMapper))
        {
            $this->fieldMapper = new FieldMapper();
            $this->fieldMapper->addFieldMapping('name', CourseGroup::class_name(), CourseGroup::PROPERTY_NAME);
        }
        return $this->fieldMapper;
    }
}
