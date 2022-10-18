<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Table;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

class RepositoryTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        $contentObjectService = new ContentObjectService(new ContentObjectRepository());
        $filterData = FilterData::getInstance($this->get_component()->get_repository_browser()->getWorkspace());

        return $contentObjectService->countContentObjectsByTypeForWorkspace(
            $filterData->getTypeDataClass(), $this->get_component()->get_repository_browser()->getWorkspace(),
            ConditionFilterRenderer::factory(
                $filterData, $this->get_component()->get_repository_browser()->getWorkspace()
            )
        );
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $filterData = FilterData::getInstance($this->get_component()->get_repository_browser()->getWorkspace());
        $contentObjectService = new ContentObjectService(new ContentObjectRepository());

        return $contentObjectService->getContentObjectsByTypeForWorkspace(
            $filterData->getTypeDataClass(), $this->get_component()->get_repository_browser()->getWorkspace(),
            ConditionFilterRenderer::factory(
                $filterData, $this->get_component()->get_repository_browser()->getWorkspace()
            ), $count, $offset, $orderBy
        );
    }
}