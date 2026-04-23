<?php
namespace Chamilo\Core\Home\Service;

use Chamilo\Core\Home\Architecture\Domain\BlockRendererRegistry;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\Storage\Repository\HomeRepository;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Service\DisplayOrderHandler;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Home\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
readonly class HomeService
{
    public function __construct(
        protected HomeRepository $homeRepository, protected SessionInterface $session, protected Translator $translator,
        protected BlockRendererRegistry $blockRendererFactory, protected ClassnameUtilities $classnameUtilities,
        protected DisplayOrderHandler $displayOrderHandler
    )
    {
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Storage\DataClass\Element>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findElementsByTypeAndParentIdentifier(
        string $type, string $parentIdentifier = DataClass::EMPTY_UUID
    ): ArrayCollection
    {
        return $this->homeRepository->findElementsByTypeAndParentIdentifier(
            $type, $parentIdentifier
        );
    }

    public function isActiveTab(int $tabKey, Element $tab, ?int $currentTabIdentifier = null): bool
    {
        return ($currentTabIdentifier == $tab->getId() || (!isset($currentTabIdentifier) && $tabKey == 0));
    }
}