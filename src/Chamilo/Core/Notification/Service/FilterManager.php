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
     * @var \Chamilo\Core\Notification\Service\NotificationContextManager
     */
    protected $notificationContextManager;

    /**
     * FilterManager constructor.
     *
     * @param \Chamilo\Core\Notification\Storage\Repository\FilterRepository $filterRepository
     * @param \Chamilo\Core\Notification\Service\NotificationContextManager $notificationContextManager
     * @param NotificationTranslator $notificationTranslator
     */
    public function __construct(
        FilterRepository $filterRepository, NotificationContextManager $notificationContextManager, NotificationTranslator $notificationTranslator
    )
    {
        $this->filterRepository = $filterRepository;
        $this->notificationTranslator = $notificationTranslator;
        $this->notificationContextManager = $notificationContextManager;
    }

    /**
     * @param string $contextPath
     * @param \Chamilo\Core\Notification\Domain\TranslationContext $translationContext
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\Filter
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getOrCreateFilterByContextPath($contextPath, TranslationContext $translationContext)
    {
        $notificationContext = $this->notificationContextManager->getOrCreateContextByPath($contextPath);
        $filter = $this->filterRepository->findFilterByNotificationContext($notificationContext);

        if(!$filter instanceof Filter)
        {
            $filter = new Filter();

            $descriptionContext = $this->notificationTranslator->translateToAllLanguagesAndEncode($translationContext);

            $filter->setDescriptionContext($descriptionContext)
                ->setNotificationContext($notificationContext);

            $this->filterRepository->createFilter($filter);
        }

        return $filter;
    }
}