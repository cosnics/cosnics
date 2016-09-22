<?php
namespace Chamilo\Core\Repository\Viewer\Table\ContentObject;

use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

/**
 * This class represents a data provider for a publication candidate table
 */
class ContentObjectTableDataProvider extends DataClassTableDataProvider
{

    /*
     * Inherited
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $contentObjectService = new ContentObjectService(new ContentObjectRepository());

        return $contentObjectService->getContentObjectsByTypeForWorkspace(
            ContentObject::class_name(),
            $this->get_component()->getWorkspace(),
            ConditionFilterRenderer::factory(
                $this->get_component()->getFilterData(),
                $this->get_component()->getWorkspace()),
            $count,
            $offset,
            $order_property);
    }

    /*
     * Inherited
     */
    public function count_data($condition)
    {
        $contentObjectService = new ContentObjectService(new ContentObjectRepository());

        return $contentObjectService->countContentObjectsByTypeForWorkspace(
            ContentObject::class_name(),
            $this->get_component()->getWorkspace(),
            ConditionFilterRenderer::factory(
                $this->get_component()->getFilterData(),
                $this->get_component()->getWorkspace()));
    }
}
