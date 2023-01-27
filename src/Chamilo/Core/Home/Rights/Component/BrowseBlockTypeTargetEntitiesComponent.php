<?php
namespace Chamilo\Core\Home\Rights\Component;

use Chamilo\Core\Home\Repository\HomeRepository;
use Chamilo\Core\Home\Rights\Manager;
use Chamilo\Core\Home\Rights\Service\BlockTypeRightsService;
use Chamilo\Core\Home\Rights\Storage\Repository\RightsRepository;
use Chamilo\Core\Home\Rights\Table\BlockTypeTargetEntityTableRenderer;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Browses the target entities for the block types
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BrowseBlockTypeTargetEntitiesComponent extends Manager
{

    /**
     * Executes this component and renders it's output
     */
    public function run()
    {
        if (!$this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $html = [];

        $tableContent = $this->renderTable();

        $html[] = $this->renderHeader();
        $html[] = $tableContent;
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getBlockTypeTargetEntityTableRenderer(): BlockTypeTargetEntityTableRenderer
    {
        return $this->getService(BlockTypeTargetEntityTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    protected function renderTable(): string
    {
        $blockTypeRightsService = new BlockTypeRightsService(new RightsRepository(), new HomeRepository());
        $blockTypes = $blockTypeRightsService->getBlockTypesWithTargetEntities();

        $totalNumberOfItems = count($blockTypes);
        $blockTypeTargetEntityTableRenderer = $this->getBlockTypeTargetEntityTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $blockTypeTargetEntityTableRenderer->getParameterNames(),
            $blockTypeTargetEntityTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $orderBy = $blockTypeTargetEntityTableRenderer->determineOrderBy($tableParameterValues);
        $offset = $tableParameterValues->getOffset();
        $count = $tableParameterValues->getNumberOfItemsPerPage();

        $compareModifier = $orderBy->getFirst()->getDirection() != SORT_DESC ? 1 : - 1;

        usort(
            $blockTypes, function ($item1, $item2) use ($compareModifier) {
            return strcmp($item1['block_type'], $item2['block_type']) * $compareModifier;
        }
        );

        $blockTypes = array_splice($blockTypes, $offset, $count);

        return $blockTypeTargetEntityTableRenderer->render($tableParameterValues, new ArrayCollection($blockTypes));
    }
}