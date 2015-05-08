<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Table;

use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;

class RepositoryTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $orderProperty = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $orderProperty);

        $contentObjectService = new ContentObjectService(new ContentObjectRepository());
        $contentObjects = $contentObjectService->getContentObjectsForWorkspace(
            $this->get_component()->get_repository_browser()->getWorkspace(),
            $count,
            $offset,
            $orderProperty);
//         var_dump($contentObjects->as_array());

        return DataManager :: retrieve_active_content_objects($this->get_table()->get_type(), $parameters);
    }

    public function count_data($condition)
    {
        $parameters = new DataClassCountParameters($condition);
        return DataManager :: count_active_content_objects($this->get_table()->get_type(), $parameters);
    }
}