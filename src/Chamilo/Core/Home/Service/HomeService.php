<?php
namespace Chamilo\Core\Home\Service;

use Chamilo\Core\Home\Repository\HomeRepository;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\Repository\Home\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class HomeService
{

    /**
     *
     * @var \Chamilo\Core\Home\Repository\HomeRepository
     */
    private $homeRepository;

    /**
     *
     * @param \Chamilo\Core\Home\Repository\HomeRepository $homeRepository
     */
    public function __construct(HomeRepository $homeRepository)
    {
        $this->homeRepository = $homeRepository;
    }

    /**
     *
     * @return \Chamilo\Core\Home\Repository\HomeRepository
     */
    public function getHomeRepository()
    {
        return $this->homeRepository;
    }

    /**
     *
     * @param \Chamilo\Core\Home\Repository\HomeRepository $homeRepository
     */
    public function setHomeRepository($homeRepository)
    {
        $this->homeRepository = $homeRepository;
    }

    /**
     *
     * @param integer $userIdentifier
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function getElementsByUserIdentifier($userIdentifier)
    {
        return $this->getHomeRepository()->findElementsByUserIdentifier($userIdentifier);
    }

    /**
     *
     * @param integer $identifier
     * @return integer
     */
    public function countElementsByUserIdentifier($userIdentifier)
    {
        return $this->getHomeRepository()->countElementsByUserIdentifier($userIdentifier);
    }

    /**
     *
     * @param integer $userIdentifier
     */
    public function createDefaultHomeByUserIdentifier($userIdentifier)
    {
        $defaultElementResultSet = $this->getElementsByUserIdentifier(0);
        $defaultElements = array();

        $elementIdentifierMap = array();

        while ($defaultElement = $defaultElementResultSet->next_result())
        {
            $defaultElements[$defaultElement->get_type()][$defaultElement->getParentId()][] = $defaultElement;
        }

        // Process tabs
        $this->createDefaultElementsByUserIdentifier(
            Tab :: class_name(),
            $defaultElements,
            $elementIdentifierMap,
            $userIdentifier);

        // Process columns
        $this->createDefaultElementsByUserIdentifier(
            Column :: class_name(),
            $defaultElements,
            $elementIdentifierMap,
            $userIdentifier);

        // Process blocks
        $this->createDefaultElementsByUserIdentifier(
            Block :: class_name(),
            $defaultElements,
            $elementIdentifierMap,
            $userIdentifier);

        return true;
    }

    /**
     *
     * @param string $elementType
     * @param \Chamilo\Core\Home\Storage\DataClass\Element[] $defaultElements
     * @param integer[] $elementIdentifierMap
     * @param integer $userIdentifier
     */
    private function createDefaultElementsByUserIdentifier($elementType, $defaultElements, &$elementIdentifierMap,
        $userIdentifier)
    {
        foreach ($defaultElements[$elementType] as $typeParentId => $typeElements)
        {
            foreach ($typeElements as $typeElement)
            {
                return $this->createDefaultElementByUserIdentifier($elementIdentifierMap, $typeElement, $userIdentifier);
            }
        }
    }

    private function createDefaultElementByUserIdentifier(&$elementIdentifierMap, Element $element, $userIdentifier)
    {
        $originalIdentifier = $element->getId();

        $element->setUserId($userIdentifier);

        if (! $element->isOnTopLevel())
        {
            $element->setParentId($elementIdentifierMap[$element->getParentId()]);
        }

        $result = $element->create();

        $elementIdentifierMap[$originalIdentifier] = $element->get_id();

        if (! $result)
        {
            throw new \Exception(Translation :: get('HomepageDefaultCreationFailed'));
        }

        return true;
    }
}