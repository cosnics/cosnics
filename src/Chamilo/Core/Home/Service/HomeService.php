<?php
namespace Chamilo\Core\Home\Service;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Repository\HomeRepository;
use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Home\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class HomeService
{
    public const PARAM_TAB_ID = 'tab';

    protected ConfigurationConsulter $configurationConsulter;

    protected ElementRightsService $elementRightsService;

    protected SessionInterface $session;

    protected Translator $translator;

    private HomeRepository $homeRepository;

    public function __construct(
        HomeRepository $homeRepository, ElementRightsService $elementRightsService, SessionInterface $session,
        ConfigurationConsulter $configurationConsulter, Translator $translator
    )
    {
        $this->homeRepository = $homeRepository;
        $this->elementRightsService = $elementRightsService;
        $this->session = $session;
        $this->configurationConsulter = $configurationConsulter;
        $this->translator = $translator;
    }

    public function countElementsByParentIdentifier(string $parentIdentifier): int
    {
        return $this->getHomeRepository()->countElementsByParentIdentifier($parentIdentifier);
    }

    public function countElementsByUserIdentifier(string $userIdentifier): int
    {
        return $this->getHomeRepository()->countElementsByUserIdentifier($userIdentifier);
    }

    /**
     * @param string[] $elementIdentifierMap
     *
     * @throws \Exception
     */
    private function createDefaultElementByUserIdentifier(
        array &$elementIdentifierMap, Element $element, string $userIdentifier
    ): bool
    {
        $originalIdentifier = $element->getId();

        $element->setUserId($userIdentifier);

        if (!$element->isOnTopLevel())
        {
            $element->setParentId($elementIdentifierMap[$element->getParentId()]);
        }

        if (!$this->getHomeRepository()->createElement($element))
        {
            throw new Exception($this->getTranslator()->trans('HomepageDefaultCreationFailed', [], Manager::CONTEXT));
        }

        $elementIdentifierMap[$originalIdentifier] = $element->getId();

        return true;
    }

    /**
     * @param \Chamilo\Core\Home\Storage\DataClass\Element[] $defaultElements
     * @param string[] $elementIdentifierMap
     *
     * @throws \Exception
     */
    private function createDefaultElementsByUserIdentifier(
        string $elementType, array $defaultElements, array &$elementIdentifierMap, string $userIdentifier
    ): bool
    {
        foreach ($defaultElements[$elementType] as $typeElements)
        {
            foreach ($typeElements as $typeElement)
            {
                if (!$this->createDefaultElementByUserIdentifier($elementIdentifierMap, $typeElement, $userIdentifier))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @throws \Exception
     */
    public function createDefaultHomeByUserIdentifier(User $user): bool
    {
        $defaultElementResultSet = $this->getElementsByUserIdentifier('0');
        $defaultElements = [];

        $elementIdentifierMap = [];

        foreach ($defaultElementResultSet as $defaultElement)
        {
            if ($this->getElementRightsService()->canUserViewElement($user, $defaultElement))
            {
                $defaultElements[$defaultElement->getType()][$defaultElement->getParentId()][] = $defaultElement;
            }
        }

        // Process tabs
        if (!$this->createDefaultElementsByUserIdentifier(
            Element::TYPE_TAB, $defaultElements, $elementIdentifierMap, $user->getId()
        ))
        {
            return false;
        }

        // Process columns
        if ($this->createDefaultElementsByUserIdentifier(
            Element::TYPE_COLUMN, $defaultElements, $elementIdentifierMap, $user->getId()
        ))
        {
            return false;
        }

        // Process blocks
        if ($this->createDefaultElementsByUserIdentifier(
            Element::TYPE_BLOCK, $defaultElements, $elementIdentifierMap, $user->getId()
        ))
        {
            return false;
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function createElement(Element $element): bool
    {
        return $this->getHomeRepository()->createElement($element);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
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

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
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

    public function determineHomeUserIdentifier(User $user = null): string
    {
        $generalMode = $this->getSession()->get('Chamilo\Core\Home\General');

        // Get user id
        if ($user instanceof User && $generalMode && $user->is_platform_admin())
        {
            return '0';
        }
        elseif ($this->isUserHomeAllowed() && $user instanceof User)
        {
            return $user->getId();
        }
        else
        {
            return '0';
        }
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function determineUser(?User $currentUser = null, bool $isGeneralMode = false): ?User
    {
        $userHomeAllowed = $this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, 'allow_user_home']);

        if ($currentUser instanceof User)
        {
            if ($isGeneralMode && $currentUser->isPlatformAdmin())
            {
                return null;
            }
            elseif ($userHomeAllowed)
            {
                return $currentUser;
            }
            elseif ($currentUser->isPlatformAdmin())
            {
                return null;
            }
            else
            {
                throw new NotAllowedException();
            }
        }
        else
        {
            throw new NotAllowedException();
        }
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function determineUserId(?User $currentUser = null, bool $isGeneralMode = false): string
    {
        $user = $this->determineUser($currentUser, $isGeneralMode);

        return $user instanceof User ? $user->getId() : '0';
    }

    public function elementHasChildren(Element $element): bool
    {
        return $this->countElementsByParentIdentifier($element->getId()) > 0;
    }

    /**
     * @param string $tabIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Storage\DataClass\Element>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findBlocksForTabIdentifier(string $tabIdentifier): ArrayCollection
    {
        return $this->getHomeRepository()->findBlocksForColumnIdentifiers(
            $this->findColumnIdentifiersForTabIdentifier($tabIdentifier)
        );
    }

    /**
     * @return string[]
     */
    public function findColumnIdentifiersForTabIdentifier(string $tabIdentifier): array
    {
        return $this->getHomeRepository()->findColumnIdentifiersForTabIdentifier($tabIdentifier);
    }

    public function findElementByIdentifier(string $elementIdentifier): ?Element
    {
        return $this->getHomeRepository()->findElementByIdentifier($elementIdentifier);
    }

    /**
     * @param string $parentIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Storage\DataClass\Element>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findElementsByParentIdentifier(string $parentIdentifier): ArrayCollection
    {
        return $this->getHomeRepository()->findElementsByParentIdentifier($parentIdentifier);
    }

    /**
     * @param string $type
     * @param ?\Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $parentIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Storage\DataClass\Element>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Exception
     */
    public function findElementsByTypeUserAndParentIdentifier(
        string $type, ?User $user = null, string $parentIdentifier = '0'
    ): ArrayCollection
    {
        $homeUserIdentifier = $this->determineHomeUserIdentifier($user);

        if ($this->isUserHomeAllowed() && $user instanceof User)
        {
            if ($this->countElementsByUserIdentifier($homeUserIdentifier) == 0)
            {
                $this->createDefaultHomeByUserIdentifier($user);
            }
        }

        return $this->getHomeRepository()->findElementsByTypeUserIdentifierAndParentIdentifier(
            $type, $homeUserIdentifier, $parentIdentifier
        );
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getCurrentTabIdentifier(ChamiloRequest $request): int
    {
        return $request->query->get(self::PARAM_TAB_ID);
    }

    public function getElementByIdentifier(string $elementIdentifier): ?Element
    {
        return $this->getHomeRepository()->findElementByIdentifier($elementIdentifier);
    }

    public function getElementRightsService(): ElementRightsService
    {
        return $this->elementRightsService;
    }

    /**
     * @param string $userIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Storage\DataClass\Element>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
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

    public function isUserHomeAllowed(): bool
    {
        return (boolean) $this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, 'allow_user_home']);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function tabByUserAndIdentifierHasMultipleColumns(string $tabIdentifier, User $user = null): bool
    {
        return $this->findElementsByTypeUserAndParentIdentifier(Element::TYPE_COLUMN, $user, $tabIdentifier)->count() >
            1;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
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
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function updateElement(Element $element): bool
    {
        return $this->getHomeRepository()->updateElement($element);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function userHasMultipleTabs(User $user = null): bool
    {
        return $this->findElementsByTypeUserAndParentIdentifier(Element::TYPE_TAB, $user)->count() > 1;
    }
}