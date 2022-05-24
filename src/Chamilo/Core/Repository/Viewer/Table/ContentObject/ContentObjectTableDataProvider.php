<?php
namespace Chamilo\Core\Repository\Viewer\Table\ContentObject;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Viewer\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * This class represents a data provider for a publication candidate table
 */
class ContentObjectTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        $contentObjectService = new ContentObjectService(new ContentObjectRepository());

        /** @var FilterData $filterData */
        $filterData = $this->get_component()->getFilterData();

        return $contentObjectService->countContentObjectsByTypeForWorkspace(
            $filterData->getTypeDataClass(), $this->get_component()->getWorkspace(), new ConditionFilterRenderer(
                $filterData, $this->get_component()->getWorkspace()
            )
        );
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        $contentObjectService = new ContentObjectService(new ContentObjectRepository());

        /** @var FilterData $filterData */
        $filterData = $this->get_component()->getFilterData();

        return $contentObjectService->getContentObjectsByTypeForWorkspace(
            $filterData->getTypeDataClass(), $this->get_component()->getWorkspace(), new ConditionFilterRenderer(
            $filterData, $this->get_component()->getWorkspace()
        ), $count, $offset, $orderBy
        );
    }
}
