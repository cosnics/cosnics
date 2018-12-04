<?php
namespace Chamilo\Libraries\Rights\Interfaces;

use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * @package Chamilo\Libraries\Rights\Entity
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface RightsEntityProvider
{

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countEntityItems(Condition $condition = null);

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperties
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[]
     */
    public function findEntityItems(
        Condition $condition = null, int $offset = null, int $count = null, array $orderProperties = null
    );

    /**
     * @param integer $entityIdentifier
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement
     */
    public function getEntityElementFinderElement(int $entityIdentifier);

    /**
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType
     */
    public function getEntityElementFinderType();

    /**
     * @param integer $userIdentifier
     *
     * @return integer[]
     */
    public function getEntityItemIdentifiersForUserIdentifier($userIdentifier);

    /**
     * @return string
     * @todo Not used much, might not be necessary
     */
    public function getEntityName();

    /**
     * @return string
     * @todo Not used much, might not be necessary
     */
    public function getEntityTranslatedName();

    /**
     * @return string
     * @todo Not used much, might not be necessary
     */
    public function getEntityType();
}
