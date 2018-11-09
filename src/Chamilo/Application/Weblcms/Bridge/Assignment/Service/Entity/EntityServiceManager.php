<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntityServiceManager
{
    /**
     * @var \Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceInterface[]
     */
    protected $entityServicesByType;

    /**
     * EntityServiceManager constructor.
     */
    public function __construct()
    {
        $this->entityServicesByType = [];
    }

    /**
     * @param int $entityType
     *
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceInterface $entityService
     */
    public function addEntityService($entityType, EntityServiceInterface $entityService)
    {
        $this->entityServicesByType[$entityType] = $entityService;
    }

    /**
     * @param int $entityType
     *
     * @return \Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceInterface|mixed
     */
    public function getEntityServiceByType($entityType)
    {
        if(!array_key_exists($entityType, $this->entityServicesByType))
        {
            throw new \InvalidArgumentException(sprintf('The given entityType %s is not supported', $entityType));
        }

        return $this->entityServicesByType[$entityType];
    }
}