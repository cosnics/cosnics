<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Table\WikiPage;

use Chamilo\Core\Repository\ContentObject\Wiki\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

class WikiPageTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        $parameters = new DataClassCountParameters($condition);

        return DataManager::count_complex_wiki_pages(
            ComplexContentObjectItem::class, $parameters
        );
    }

    /*
     * Inherited<
     */

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $orderBy);

        return DataManager::retrieve_complex_wiki_pages(
            ComplexContentObjectItem::class, $parameters
        );
    }
}
