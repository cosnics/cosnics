<?php
namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class EvaluationEntityServiceManager
{
    /**
     * @var EvaluationEntityServiceInterface[]
     */
    protected $entityServicesByType;

    /**
     * EvaluationEntityServiceManager constructor.
     */
    public function __construct()
    {
        $this->entityServicesByType = [];
    }

    /**
     * @param int $entityType
     *
     * @param EvaluationEntityServiceInterface $entityService
     */
    public function addEntityService(int $entityType, EvaluationEntityServiceInterface $entityService)
    {
        $this->entityServicesByType[$entityType] = $entityService;
    }

    /**
     * @param int $entityType
     *
     * @return EvaluationEntityServiceInterface|mixed
     */
    public function getEntityServiceByType(int $entityType)
    {
        if (!array_key_exists($entityType, $this->entityServicesByType))
        {
            throw new \InvalidArgumentException(sprintf('The given entityType %s is not supported', $entityType));
        }

        return $this->entityServicesByType[$entityType];
    }
}