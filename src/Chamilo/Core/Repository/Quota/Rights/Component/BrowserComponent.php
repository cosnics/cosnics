<?php
namespace Chamilo\Core\Repository\Quota\Rights\Component;

use Chamilo\Core\Repository\Quota\Rights\Manager;
use Chamilo\Core\Repository\Quota\Rights\Table\EntityTableRenderer;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Doctrine\Common\Collections\ArrayCollection;

class BrowserComponent extends Manager
{

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Exception
     */
    public function run()
    {
        if (!$this->getRightsService()->canUserSetRightsForQuotaRequests($this->getUser()))
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
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Exception
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = $this->getRightsService()->countAllRightsLocationEntityRightGroups();
        $entityTableRenderer = $this->getEntityTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $entityTableRenderer->getParameterNames(), $entityTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $entities = $this->getRightsService()->getRightsLocationEntityRightGroupsWithEntityAndGroup(
            $tableParameterValues->getOffset(), $tableParameterValues->getNumberOfItemsPerPage(),
            $entityTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $entityTableRenderer->render($tableParameterValues, new ArrayCollection($entities));
    }
}
