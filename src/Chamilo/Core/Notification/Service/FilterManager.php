<?php

namespace Chamilo\Core\Notification\Service;

use Chamilo\Core\Notification\Domain\TranslationContext;
use Chamilo\Core\Notification\Storage\Entity\Filter;
use Chamilo\Core\Notification\Storage\Repository\FilterRepository;

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
     * @var NotificationTranslator
     */
    protected $notificationTranslator;

    /**
     * FilterManager constructor.
     *
     * @param \Chamilo\Core\Notification\Storage\Repository\FilterRepository $filterRepository
     * @param NotificationTranslator $notificationTranslator
     */
    public function __construct(
        FilterRepository $filterRepository, NotificationTranslator $notificationTranslator
    )
    {
        $this->filterRepository = $filterRepository;
        $this->notificationTranslator = $notificationTranslator;
    }

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

            $descriptionContext = $this->notificationTranslator->translateToAllLanguagesAndEncode($translationContext);

            $filter->setDescriptionContext($descriptionContext)
                ->setPath($filterPath);

            $this->filterRepository->createFilter($filter);
        }

        return $filter;
    }
}