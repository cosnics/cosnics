<?php
namespace Chamilo\Core\Home\Service;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Repository\HomeRepository;
use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Translation\Translation;
use Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @package Chamilo\Core\Repository\Home\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class HomeService
{
    public const PARAM_TAB_ID = 'tab';

    /**
     * @var ElementRightsService
     */
    protected $elementRightsService;

    protected SessionInterface $session;

    /**
     * @var \Chamilo\Core\Home\Repository\HomeRepository
     */
    private $homeRepository;

    /**
     * @param \Chamilo\Core\Home\Repository\HomeRepository $homeRepository
     * @param ElementRightsService $elementRightsService
     */
    public function __construct(
        HomeRepository $homeRepository, ElementRightsService $elementRightsService, SessionInterface $session
    )
    {
        $this->homeRepository = $homeRepository;
        $this->elementRightsService = $elementRightsService;
        $this->session = $session;
    }

    /**
     * @param int $identifier
     *
     * @return int
     */
    public function countElementsByUserIdentifier($userIdentifier)
    {
        return $this->getHomeRepository()->countElementsByUserIdentifier($userIdentifier);
    }

    private function createDefaultElementByUserIdentifier(&$elementIdentifierMap, Element $element, $userIdentifier)
    {
        $originalIdentifier = $element->getId();

        $element->setUserId($userIdentifier);

        if (!$element->isOnTopLevel())
        {
            $element->setParentId($elementIdentifierMap[$element->getParentId()]);
        }

        $result = $element->create();

        $elementIdentifierMap[$originalIdentifier] = $element->get_id();

        if (!$result)
        {
            throw new Exception(Translation::get('HomepageDefaultCreationFailed'));
        }

        return true;
    }

    /**
     * @param string $elementType
     * @param \Chamilo\Core\Home\Storage\DataClass\Element[] $defaultElements
     * @param int $elementIdentifierMap
     * @param int $userIdentifier
     */
    private function createDefaultElementsByUserIdentifier(
        $elementType, $defaultElements, &$elementIdentifierMap, $userIdentifier
    )
    {
        foreach ($defaultElements[$elementType] as $typeParentId => $typeElements)
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
     * @param User $user
     *
     * @return bool
     */
    public function createDefaultHomeByUserIdentifier(User $user)
    {
        $defaultElementResultSet = $this->getElementsByUserIdentifier(0);
        $defaultElements = [];

        $elementIdentifierMap = [];

        foreach ($defaultElementResultSet as $defaultElement)
        {
            if ($this->elementRightsService->canUserViewElement($user, $defaultElement))
            {
                $defaultElements[$defaultElement->getType()][$defaultElement->getParentId()][] = $defaultElement;
            }
        }

        // Process tabs
        $this->createDefaultElementsByUserIdentifier(
            Tab::class, $defaultElements, $elementIdentifierMap, $user->getId()
        );

        // Process columns
        $this->createDefaultElementsByUserIdentifier(
            Column::class, $defaultElements, $elementIdentifierMap, $user->getId()
        );

        // Process blocks
        $this->createDefaultElementsByUserIdentifier(
            Block::class, $defaultElements, $elementIdentifierMap, $user->getId()
        );

        return true;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return int
     */
    public function determineHomeUserIdentifier(User $user = null)
    {
        if (!isset($this->homeUserIdentifier))
        {
            $userHomeAllowed = Configuration::getInstance()->get_setting([Manager::CONTEXT, 'allow_user_home']);
            $generalMode = $this->getSession()->get('Chamilo\Core\Home\General');

            // Get user id
            if ($user instanceof User && $generalMode && $user->is_platform_admin())
            {
                $this->homeUserIdentifier = 0;
            }
            elseif ($userHomeAllowed && $user instanceof User)
            {
                $this->homeUserIdentifier = $user->get_id();
            }
            else
            {
                $this->homeUserIdentifier = 0;
            }
        }

        return $this->homeUserIdentifier;
    }

    /**
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     *
     * @return int
     */
    public function getCurrentTabIdentifier(ChamiloRequest $request)
    {
        return $request->query->get(self::PARAM_TAB_ID);
    }

    /**
     * Returns a home element by an identifier
     *
     * @param int $elementIdentifier
     *
     * @return Element
     */
    public function getElementByIdentifier($elementIdentifier)
    {
        return $this->getHomeRepository()->findElementByIdentifier($elementIdentifier);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $type
     * @param int $parentIdentifier
     */
    public function getElements(User $user = null, $type, $parentIdentifier = 0)
    {
        if (!isset($this->elements))
        {
            $homeUserIdentifier = $this->determineHomeUserIdentifier($user);
            $userHomeAllowed = Configuration::getInstance()->get_setting([Manager::CONTEXT, 'allow_user_home']);

            if ($userHomeAllowed && $user instanceof User)
            {
                if ($this->countElementsByUserIdentifier($homeUserIdentifier) == 0)
                {
                    $this->createDefaultHomeByUserIdentifier($user);
                }
            }

            $elementsResultSet = $this->getElementsByUserIdentifier($homeUserIdentifier);

            foreach ($elementsResultSet as $element)
            {
                $this->elements[$element->getType()][$element->getParentId()][] = $element;
            }
        }

        if (isset($this->elements[$type]) && isset($this->elements[$type][$parentIdentifier]))
        {
            return $this->elements[$type][$parentIdentifier];
        }
        else
        {
            return [];
        }
    }

    /**
     * @param int $userIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getElementsByUserIdentifier($userIdentifier)
    {
        return $this->getHomeRepository()->findElementsByUserIdentifier($userIdentifier);
    }

    /**
     * @return \Chamilo\Core\Home\Repository\HomeRepository
     */
    public function getHomeRepository()
    {
        return $this->homeRepository;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function isActiveTab(int $tabKey, Tab $tab, ?int $currentTabIdentifier = null): bool
    {
        return ($currentTabIdentifier == $tab->getId() || (!isset($currentTabIdentifier) && $tabKey == 0));
    }

    /**
     * @return bool
     */
    public function isInGeneralMode()
    {
        return (boolean) $this->getSession()->get('Chamilo\Core\Home\General');
    }

    /**
     * @return bool
     */
    public function isUserHomeAllowed()
    {
        return (boolean) Configuration::getInstance()->get_setting([Manager::CONTEXT, 'allow_user_home']);
    }

    /**
     * @param \Chamilo\Core\Home\Repository\HomeRepository $homeRepository
     */
    public function setHomeRepository($homeRepository)
    {
        $this->homeRepository = $homeRepository;
    }

    public function tabByUserAndIdentifierHasMultipleColumns(User $user = null, $tabIdentifier)
    {
        $columns = $this->getElements($user, Column::class, $tabIdentifier);

        return count($columns) > 1;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return bool
     */
    public function userHasMultipleTabs(User $user = null)
    {
        $tabs = $this->getElements($user, Tab::class);

        return count($tabs) > 1;
    }
}