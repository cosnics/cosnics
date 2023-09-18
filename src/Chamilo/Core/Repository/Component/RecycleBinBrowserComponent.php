<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Table\RecycleBinTableRenderer;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class RecycleBinBrowserComponent extends Manager
{
    private ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function run()
    {
        $translator = $this->getTranslator();
        $trail = $this->getBreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), $translator->trans('RecycleBin', [], Manager::CONTEXT)));

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $html = [];

        $html[] = $this->render_header();

        if ($this->getRequest()->query->has(self::PARAM_EMPTY_RECYCLE_BIN))
        {
            $this->emptyRecycleBin();
            $html[] = $this->display_message(
                htmlentities($translator->trans('RecycleBinEmptied', [], Manager::CONTEXT))
            );
        }

        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $this->renderTable();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([self::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS]),
                $this->getTranslator()->trans('RepositoryManagerBrowserComponent', [], Manager::CONTEXT)
            )
        );
    }

    /**
     * Empty the recycle bin.
     * This function will permanently delete all objects from the recycle bin. Only objects from
     * current user will be deleted.
     */
    private function emptyRecycleBin()
    {
        $parameters = new DataClassRetrievesParameters($this->getRecycleBinTableCondition());
        $trashed_objects = DataManager::retrieve_active_content_objects(ContentObject::class, $parameters);

        foreach ($trashed_objects as $object)
        {
            $object->delete();
        }

        $this->getDataClassRepositoryCache()->truncate(ContentObject::class);
    }

    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();

            $translator = $this->getTranslator();

            $commonActions->addButton(
                new Button(
                    $translator->trans('EmptyRecycleBin', [], Manager::CONTEXT), new FontAwesomeGlyph('trash-alt'),
                    $this->get_url([self::PARAM_EMPTY_RECYCLE_BIN => 1]), ToolbarItem::DISPLAY_ICON_AND_LABEL,
                    $translator->trans('ConfirmEmptyRecycleBin', [], Manager::CONTEXT)
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getDataClassRepositoryCache(): DataClassRepositoryCache
    {
        return $this->getService(DataClassRepositoryCache::class);
    }

    public function getRecycleBinTableCondition(): AndCondition
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
            new StaticConditionVariable($this->getUser()->getId())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_STATE),
            new StaticConditionVariable(ContentObject::STATE_RECYCLED)
        );

        return new AndCondition($conditions);
    }

    public function getRecycleBinTableRenderer(): RecycleBinTableRenderer
    {
        return $this->getService(RecycleBinTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = DataManager::count_active_content_objects(
            ContentObject::class, new DataClassCountParameters($this->getRecycleBinTableCondition())
        );
        $recycleBinTableRenderer = $this->getRecycleBinTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $recycleBinTableRenderer->getParameterNames(), $recycleBinTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $contentObjects = DataManager::retrieve_active_content_objects(
            ContentObject::class, new DataClassRetrievesParameters(
                $this->getRecycleBinTableCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
                $tableParameterValues->getOffset(), $recycleBinTableRenderer->determineOrderBy($tableParameterValues)
            )
        );

        return $recycleBinTableRenderer->render($tableParameterValues, $contentObjects);
    }
}
