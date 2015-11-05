<?php
namespace Chamilo\Core\Menu\Repository;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataManager;
use Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemRepository
{

    /**
     *
     * @var \Chamilo\Core\Menu\Storage\DataClass\Item[]
     */
    private $itemsByParentIdentifier;

    /**
     *
     * @param \Chamilo\Libraries\Storage\ResultSet\ResultSet $itemResultSet
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[]
     */
    private function processItemResultSet(\Chamilo\Libraries\Storage\ResultSet\ResultSet $itemResultSet)
    {
        while ($item = $itemResultSet->next_result())
        {
            $item->get_additional_properties();
            $item->get_titles();

            if (! isset($itemsByParentIdentifier[$item->get_parent()]))
            {
                $itemsByParentIdentifier[$item->get_parent()] = array();
            }

            $itemsByParentIdentifier[$item->get_parent()][] = $item;
        }

        return $itemsByParentIdentifier;
    }

    /**
     * @param integer $parentIdentifier
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findItemsByParentIdentifier($parentIdentifier)
    {
        if (! isset($this->itemsByParentIdentifier))
        {
            $cache = new FilesystemCache(Path :: getInstance()->getCachePath(__NAMESPACE__));
            $cacheIdentifier = md5(serialize(array(__METHOD__)));

            if (! $cache->contains($cacheIdentifier))
            {
                $orderBy = array();
                $orderBy[] = new OrderBy(new PropertyConditionVariable(Item :: class_name(), Item :: PROPERTY_PARENT));
                $orderBy[] = new OrderBy(new PropertyConditionVariable(Item :: class_name(), Item :: PROPERTY_SORT));

                $itemResultSet = DataManager :: retrieves(
                    Item :: class_name(),
                    new DataClassRetrievesParameters(null, null, null, $orderBy));

                $itemsByParentIdentifier = $this->processItemResultSet($itemResultSet);

                $cache->save($cacheIdentifier, $itemsByParentIdentifier);
            }

            $this->itemsByParentIdentifier = $cache->fetch($cacheIdentifier);
        }

        return $this->itemsByParentIdentifier[$parentIdentifier];
    }
}