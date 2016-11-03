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
        $filterData = FilterData:: get_instance($this->get_component()->get_repository_browser()->getWorkspace());
        $contentObjectService = new ContentObjectService(new ContentObjectRepository());
        
        return $contentObjectService->getContentObjectsByTypeForWorkspace(
            $filterData->getTypeDataClass(),
            $this->get_component()->get_repository_browser()->getWorkspace(),
            ConditionFilterRenderer :: factory(
                $filterData,
                $this->get_component()->get_repository_browser()->getWorkspace()),
            $count,
            $offset,
            $orderProperty);

    }

    public function count_data($condition)
    {
        $filterData = FilterData:: get_instance($this->get_component()->get_repository_browser()->getWorkspace());
        $contentObjectService = new ContentObjectService(new ContentObjectRepository());
        
        return $contentObjectService->countContentObjectsByTypeForWorkspace(
            $filterData->getTypeDataClass(),
            $this->get_component()->get_repository_browser()->getWorkspace(),
            ConditionFilterRenderer :: factory(
                $filterData,
                $this->get_component()->get_repository_browser()->getWorkspace()));

    }
}
