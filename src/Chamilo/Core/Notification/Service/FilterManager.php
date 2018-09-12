<?php

namespace Chamilo\Core\Notification\Service;

use Chamilo\Core\Notification\Storage\Entity\Filter;

/**
 * @package Chamilo\Core\Notification\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FilterManager
{
    /**
     * @var \Chamilo\Core\Notification\Storage\Repository\FilterRepository
     */
    protected $filterRepository;

    /**
     * @param string $filterPath
     * @param array $descriptionContext
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\Filter
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getOrCreateFilterByPath($filterPath, $descriptionContext)
    {
        $filter = $this->filterRepository->findByPath($filterPath);
        if(!$filter instanceof Filter)
        {
            $filter = new Filter();

            $filter->setDescriptionContext(json_encode($descriptionContext))
                ->setPath($filterPath);

            $this->filterRepository->createFilter($filter);
        }

        return $filter;
    }
}