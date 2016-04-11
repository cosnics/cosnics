<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Gallery;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\DataClassGalleryTable\DataClassGalleryTableDataProvider;

class GalleryTableDataProvider extends DataClassGalleryTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $orderProperty = null)
    {
        $contentObjectService = new ContentObjectService(new ContentObjectRepository());
        return $contentObjectService->getContentObjectsForWorkspace(
            $this->get_component()->get_repository_browser()->getWorkspace(),
            ConditionFilterRenderer :: factory(
                FilterData :: get_instance($this->get_component()->get_repository_browser()->getWorkspace()),
                $this->get_component()->get_repository_browser()->getWorkspace()),
            $count,
            $offset,
            $orderProperty);

        // return DataManager :: retrieve_active_content_objects(
        // $this->get_table()->get_type(),
        // new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    public function count_data($condition)
    {
        $contentObjectService = new ContentObjectService(new ContentObjectRepository());
        return $contentObjectService->countContentObjectsForWorkspace(
            $this->get_component()->get_repository_browser()->getWorkspace(),
            ConditionFilterRenderer :: factory(
                FilterData :: get_instance($this->get_component()->get_repository_browser()->getWorkspace()),
                $this->get_component()->get_repository_browser()->getWorkspace()));

        // return DataManager :: count_active_content_objects(
        // $this->get_table()->get_type(),
        // new DataClassCountParameters($condition));
    }
}
