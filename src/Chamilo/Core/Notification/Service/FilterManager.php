<?php

namespace Chamilo\Core\Notification\Service;

use Chamilo\Core\Notification\Domain\TranslationContext;
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
     * @var \Chamilo\Core\Notification\Service\NotificationTranslator
     */
    protected $notificationTranslator;

    /**
     * @param string $filterPath
     * @param \Chamilo\Core\Notification\Domain\TranslationContext $translationContext
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\Filter
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getOrCreateFilterByPath($filterPath, TranslationContext $translationContext)
    {
        $filter = $this->filterRepository->findByPath($filterPath);
        if(!$filter instanceof Filter)
        {
            $filter = new Filter();

            $descriptionContext = $this->notificationTranslator->createNotificationTranslations($translationContext);

            $filter->setDescriptionContext($descriptionContext)
                ->setPath($filterPath);

            $this->filterRepository->createFilter($filter);
        }

        return $filter;
    }
}