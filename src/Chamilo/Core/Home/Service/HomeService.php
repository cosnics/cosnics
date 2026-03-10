<?php
namespace Chamilo\Core\Home\Service;

use Chamilo\Core\Home\Architecture\Domain\BlockRendererRegistry;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\Storage\Repository\HomeRepository;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
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
class HomeService
{
    public const PARAM_TAB_ID = 'tab';

    protected BlockRendererRegistry $blockRendererFactory;

    protected ClassnameUtilities $classnameUtilities;

    protected SessionInterface $session;

    protected Translator $translator;

    private DisplayOrderHandler $displayOrderHandler;

    private HomeRepository $homeRepository;

    public function __construct(
        HomeRepository $homeRepository, SessionInterface $session, Translator $translator,
        BlockRendererRegistry $blockRendererFactory, ClassnameUtilities $classnameUtilities,
        DisplayOrderHandler $displayOrderHandler
    )
    {
        $this->homeRepository = $homeRepository;
        $this->session = $session;
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function createElement(Element $element): bool
    {
        if (!$this->getDisplayOrderHandler()->handleDisplayOrderBeforeCreate($element)) {
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

        foreach ($childElements as $childElement) {
            if (!$this->deleteElement($childElement)) {
                return false;
            }
        }

        if (!$this->getHomeRepository()->deleteElement($element)) {
            return false;
        }

        if (!$this->getDisplayOrderHandler()->handleDisplayOrderAfterDelete($element)) {
            return false;
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
        string $type, string $parentIdentifier = DataClass::EMPTY_UUID
    ): ArrayCollection
    {
        return $this->getHomeRepository()->findElementsByTypeAndParentIdentifier(
            $type, $parentIdentifier
        );
    }

    public function getBlockRendererFactory(): BlockRendererRegistry
    {
        return $this->blockRendererFactory;
    }

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function updateElement(Element $element): bool
    {
        if (!$this->getDisplayOrderHandler()->handleDisplayOrderBeforeUpdate($element)) {
            return false;
        }

        return $this->getHomeRepository()->updateElement($element);
    }
}