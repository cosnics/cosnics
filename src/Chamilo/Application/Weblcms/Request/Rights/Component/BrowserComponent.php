<?php
namespace Chamilo\Application\Weblcms\Request\Rights\Component;

use Chamilo\Application\Weblcms\Request\Rights\Manager;
use Chamilo\Application\Weblcms\Request\Rights\Storage\DataClass\RightsLocationEntityRightGroup;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Application\Weblcms\Request\Rights\Table\EntityTableRenderer;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class BrowserComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \TableException
     */
    public function run()
    {
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getLinkTabsRenderer()->render($this->get_tabs(self::ACTION_BROWSE), $this->renderTable());
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getEntityTableRenderer(): EntityTableRenderer
    {
        return $this->getService(EntityTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Exception
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = DataManager::count(RightsLocationEntityRightGroup::class, new DataClassCountParameters());
        $entityTableRenderer = $this->getEntityTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $entityTableRenderer->getParameterNames(), $entityTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $parameters = new DataClassRetrievesParameters(
            null, $tableParameterValues->getNumberOfItemsPerPage(), $tableParameterValues->getOffset(),
            $entityTableRenderer->determineOrderBy($tableParameterValues)
        );

        $entities = DataManager::retrieves(RightsLocationEntityRightGroup::class, $parameters);

        return $entityTableRenderer->render($tableParameterValues, $entities);
    }
}