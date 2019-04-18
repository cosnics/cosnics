<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Component;

use Chamilo\Application\Weblcms\Bridge\ExternalTool\Service\ExternalToolResultService;
use Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Table\Result\ResultTable;
use Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Table\Result\ResultTableParameters;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupportedSearchFormInterface;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ViewResultComponent extends Manager implements TableSupport
{
    /**
     * @return string
     * @throws \Exception
     */
    public function run()
    {
        $html = [];

        $html[] = $this->render_header();

        $toolbarRenderer = $this->getToolbarRenderer();
        $html[] = $toolbarRenderer->render();

        $html[] = $this->getTable($toolbarRenderer->getSearchForm());
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer
     */
    protected function getToolbarRenderer()
    {
        $toolbar = new ButtonToolBar($this->get_url());
        $toolbarRenderer = new ButtonToolBarRenderer($toolbar);

        return $toolbarRenderer;
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Interfaces\TableSupportedSearchFormInterface $searchForm
     *
     * @return string
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Exception
     */
    protected function getTable(TableSupportedSearchFormInterface $searchForm)
    {
        $table = new ResultTable(
            $this,
            new ResultTableParameters($this->getExternalToolResultService(), $this->getContentObjectPublication())
        );

        $table->setSearchForm($searchForm);

        return $table->render();
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }

    /**
     * @return array|string[]
     */
    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Bridge\ExternalTool\Service\ExternalToolResultService
     */
    protected function getExternalToolResultService()
    {
        return $this->getService(ExternalToolResultService::class);
    }

    /**
     * Returns the condition
     *
     * @param string $tableClassname
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_table_condition($tableClassname)
    {
        return null;
    }
}
