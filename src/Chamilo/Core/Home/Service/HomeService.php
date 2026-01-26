<?php
namespace Chamilo\Core\Home\Service;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Home\Architecture\Domain\BlockRendererCollection;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\Storage\Repository\HomeRepository;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\ChamiloRequest;
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
class HomeService
{
    public const PARAM_TAB_ID = 'tab';

    protected BlockRendererCollection $blockRendererFactory;

    protected ClassnameUtilities $classnameUtilities;

    protected ConfigurationConsulter $configurationConsulter;

    protected SessionInterface $session;

    protected Translator $translator;

    private DisplayOrderHandler $displayOrderHandler;

    private HomeRepository $homeRepository;

    public function __construct(
        HomeRepository $homeRepository, SessionInterface $session, ConfigurationConsulter $configurationConsulter,
        Translator $translator, BlockRendererCollection $blockRendererFactory, ClassnameUtilities $classnameUtilities,
        DisplayOrderHandler $displayOrderHandler
    )
    {
        $this->homeRepository = $homeRepository;
        $this->session = $session;
        $this->configurationConsulter = $configurationConsulter;
        $this->translator = $translator;
        $this->blockRendererFactory = $blockRendererFactory;
        $this->classnameUtilities = $classnameUtilities;
        $this->displayOrderHandler = $displayOrderHandler;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countElementsByParentIdentifier(string $parentIdentifier): int
    {
        return $this->getHomeRepository()->countElementsByParentIdentifier($parentIdentifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countElementsByUserIdentifier(string $userIdentifier): int
    {
        return $this->getHomeRepository()->countElementsByUserIdentifier($userIdentifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function createElement(Element $element): bool
    {
        if (!$this->getDisplayOrderHandler()->handleDisplayOrderBeforeCreate($element))
        {
            return false;
        }

        return $this->getHomeRepository()->createElement($element);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteElement(Element $element): bool
    {
        $childElements = $this->findElementsByParentIdentifier($element->getId());

        foreach ($childElements as $childElement)
        {
            if (!$this->deleteElement($childElement))
            {
                return false;
            }
        }

        if (!$this->getHomeRepository()->deleteElement($element))
        {
            return false;
        }

        if (!$this->getDisplayOrderHandler()->handleDisplayOrderAfterDelete($element))
        {
            return false;
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteElementsForUserIdentifier(string $userIdentifier): bool
    {
        $userTabs = $this->getHomeRepository()->findElementsByTypeUserIdentifierAndParentIdentifier(
            Element::TYPE_TAB, $userIdentifier
        );

        foreach ($userTabs as $userTab)
        {
            if (!$this->deleteElement($userTab))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function elementHasChildren(Element $element): bool
    {
        return $this->countElementsByParentIdentifier($element->getId()) > 0;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Storage\DataClass\Element>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findBlocksForTabIdentifier(string $tabIdentifier): ArrayCollection
    {
        return $this->getHomeRepository()->findBlocksForColumnIdentifiers(
            $this->findColumnIdentifiersForTabIdentifier($tabIdentifier)
        );
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findColumnIdentifiersForTabIdentifier(string $tabIdentifier): array
    {
        return $this->getHomeRepository()->findColumnIdentifiersForTabIdentifier($tabIdentifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findElementByIdentifier(string $elementIdentifier): ?Element
    {
        return $this->getHomeRepository()->findElementByIdentifier($elementIdentifier);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Storage\DataClass\Element>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findElementsByParentIdentifier(string $parentIdentifier): ArrayCollection
    {
        return $this->getHomeRepository()->findElementsByParentIdentifier($parentIdentifier);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Storage\DataClass\Element>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findElementsByTypeAndParentIdentifier(
        string $type, string $parentIdentifier = '0'
    ): ArrayCollection
    {
        return $this->getHomeRepository()->findElementsByTypeUserIdentifierAndParentIdentifier(
            $type, '0', $parentIdentifier
        );
    }

    public function getBlockRendererFactory(): BlockRendererCollection
    {
        return $this->blockRendererFactory;
    }

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getCurrentTabIdentifier(ChamiloRequest $request): int
    {
        return $request->query->get(self::PARAM_TAB_ID);
    }

    public function getDisplayOrderHandler(): DisplayOrderHandler
    {
        return $this->displayOrderHandler;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getElementByIdentifier(string $elementIdentifier): ?Element
    {
        return $this->getHomeRepository()->findElementByIdentifier($elementIdentifier);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Storage\DataClass\Element>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getElementsByUserIdentifier(string $userIdentifier): ArrayCollection
    {
        return $this->getHomeRepository()->findElementsByUserIdentifier($userIdentifier);
    }

    public function getHomeRepository(): HomeRepository
    {
        return $this->homeRepository;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function isActiveTab(int $tabKey, Element $tab, ?int $currentTabIdentifier = null): bool
    {
        return ($currentTabIdentifier == $tab->getId() || (!isset($currentTabIdentifier) && $tabKey == 0));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function tabByIdentifierHasMultipleColumns(string $tabIdentifier): bool
    {
        return $this->findElementsByTypeAndParentIdentifier(Element::TYPE_COLUMN, $tabIdentifier)->count() > 1;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function tabCanBeDeleted(Element $tab): bool
    {
        $tabBlocks = $this->findBlocksForTabIdentifier($tab->getId());

        foreach ($tabBlocks as $tabBlock)
        {
            if ($tabBlock->getContext() == 'Chamilo\Core\Admin' || $tabBlock->getContext() == 'Chamilo\Core\User')
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateElement(Element $element): bool
    {
        if (!$this->getDisplayOrderHandler()->handleDisplayOrderBeforeUpdate($element))
        {
            return false;
        }

        return $this->getHomeRepository()->updateElement($element);
    }
}