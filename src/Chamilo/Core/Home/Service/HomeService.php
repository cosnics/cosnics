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
readonly class HomeService
{
    public const string PARAM_TAB_ID = 'tab';

    public function __construct(
        protected HomeRepository $homeRepository, protected SessionInterface $session, protected Translator $translator,
        protected BlockRendererRegistry $blockRendererFactory, protected ClassnameUtilities $classnameUtilities,
        protected DisplayOrderHandler $displayOrderHandler
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countElementsByParentIdentifier(string $parentIdentifier): int
    {
        return $this->homeRepository->countElementsByParentIdentifier($parentIdentifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function createElement(Element $element): bool
    {
        if (!$this->displayOrderHandler->handleDisplayOrderBeforeCreate($element)) {
            return false;
        }

        return $this->homeRepository->createElement($element);
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

        if (!$this->homeRepository->deleteElement($element)) {
            return false;
        }

        if (!$this->displayOrderHandler->handleDisplayOrderAfterDelete($element)) {
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
        return $this->homeRepository->findBlocksForColumnIdentifiers(
            $this->findColumnIdentifiersForTabIdentifier($tabIdentifier)
        );
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findColumnIdentifiersForTabIdentifier(string $tabIdentifier): array
    {
        return $this->homeRepository->findColumnIdentifiersForTabIdentifier($tabIdentifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findElementByIdentifier(string $elementIdentifier): ?Element
    {
        return $this->homeRepository->findElementByIdentifier($elementIdentifier);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Storage\DataClass\Element>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findElementsByParentIdentifier(string $parentIdentifier): ArrayCollection
    {
        return $this->homeRepository->findElementsByParentIdentifier($parentIdentifier);
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

    public function getCurrentTabIdentifier(ChamiloRequest $request): int
    {
        return $request->query->get(self::PARAM_TAB_ID);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getElementByIdentifier(string $elementIdentifier): ?Element
    {
        return $this->homeRepository->findElementByIdentifier($elementIdentifier);
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
        if (!$this->displayOrderHandler->handleDisplayOrderBeforeUpdate($element)) {
            return false;
        }

        return $this->homeRepository->updateElement($element);
    }
}