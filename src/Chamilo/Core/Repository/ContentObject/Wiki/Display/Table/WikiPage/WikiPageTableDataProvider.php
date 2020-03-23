<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Table\WikiPage;

use Chamilo\Core\Repository\ContentObject\Wiki\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class WikiPageTableDataProvider extends DataClassTableDataProvider
{

    /*
     * Inherited
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return DataManager::retrieve_complex_wiki_pages(
            ComplexContentObjectItem::class_name(), 
            $parameters);
    }

    /*
     * Inherited<
     */
    public function count_data($condition)
    {
        $parameters = new DataClassCountParameters($condition);
        return DataManager::count_complex_wiki_pages(
            ComplexContentObjectItem::class_name(), 
            $parameters);
    }
}
