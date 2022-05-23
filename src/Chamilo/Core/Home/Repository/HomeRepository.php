<?php
namespace Chamilo\Core\Home\Repository;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class HomeRepository
{

    /**
     *
     * @param integer $userIdentifier
     *
     * @return integer
     */
    public function countElementsByUserIdentifier($userIdentifier)
    {
        $parameters = new DataClassCountParameters($this->getElementsByUserIdentifierCondition($userIdentifier));

        return DataManager::count(Element::class, $parameters);
    }

    public function findBlockTypes()
    {
        $homeIntegrations = Configuration::getInstance()->getIntegrationRegistrations('Chamilo\Core\Home');
        $blockTypes = [];

        foreach ($homeIntegrations as $homeIntegration)
        {
            $className = $homeIntegration[Registration::PROPERTY_CONTEXT] . '\Manager';

            if (class_exists($className))
            {
                $homeIntegrationManager = new $className();
                $blockTypes = array_merge($blockTypes, $homeIntegrationManager->getBlockTypes());
            }
        }

        return $blockTypes;
    }

    /**
     * Finds the blocks for a given user
     *
     * @param int $userIdentifier
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findBlocksByUserIdentifier($userIdentifier)
    {
        $parameters = new DataClassRetrievesParameters(
            new EqualityCondition(
                new PropertyConditionVariable(Element::class, Block::PROPERTY_USER_ID),
                new StaticConditionVariable($userIdentifier)
            )
        );

        return DataManager::retrieves(Block::class, $parameters);
    }

    /**
     * Returns a home element by an identifier
     *
     * @param int $elementIdentifier
     *
     * @return Element
     */
    public function findElementByIdentifier($elementIdentifier)
    {
        return DataManager::retrieve_by_id(Element::class, $elementIdentifier);
    }

    /**
     *
     * @param integer $userIdentifier
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findElementsByUserIdentifier($userIdentifier)
    {
        $parameters = new DataClassRetrievesParameters(
            $this->getElementsByUserIdentifierCondition($userIdentifier), null, null, new OrderBy(array(
                new OrderProperty(new PropertyConditionVariable(Element::class, Element::PROPERTY_TYPE)),
                new OrderProperty(new PropertyConditionVariable(Element::class, Element::PROPERTY_SORT))
            ))
        );

        return DataManager::retrieves(Element::class, $parameters);
    }

    /**
     *
     * @param integer $userIdentifier
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\EqualityCondition
     */
    public function getElementsByUserIdentifierCondition($userIdentifier)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );
    }
}