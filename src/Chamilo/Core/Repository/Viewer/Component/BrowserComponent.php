<?php
namespace Chamilo\Core\Repository\Viewer\Component;

use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Selector\TypeSelectorFactory;
use Chamilo\Core\Repository\Selector\TypeSelectorTrait;
use Chamilo\Core\Repository\Viewer\Filter\FilterData;
use Chamilo\Core\Repository\Viewer\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Viewer\Manager;
use Chamilo\Core\Repository\Viewer\Menu\RepositoryCategoryMenu;
use Chamilo\Core\Repository\Viewer\Table\ContentObjectTableRenderer;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceContentObjectService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;

class BrowserComponent extends Manager
{
    use TypeSelectorTrait;

    public const PROPERTY_CATEGORY = 'category';

    public const SHARED_BROWSER = 'shared';
    public const SHARED_BROWSER_ALLOWED = 'allow_shared_browser';

    /**
     * @var ButtonToolBarRenderer
     */
    protected $buttonToolbarRenderer;

    /**
     * @var FilterData
     */
    protected $filterData;

    protected Workspace $workspace;

    public function run()
    {
        $this->checkAuthorization(\Chamilo\Core\Repository\Manager::CONTEXT);

        $this->setupFilterData();

        $buttonToolbarRender = $this->getButtonToolbarRenderer();

        $html = [];

        $html[] = $this->render_header();

        $this->registerQuery();

        if ($buttonToolbarRender)
        {
            $html[] = $buttonToolbarRender->render();
        }

        if ($this->get_maximum_select() > self::SELECT_SINGLE)
        {
            $message = sprintf(Translation::get('SelectMaximumNumberOfContentObjects'), $this->get_maximum_select());

            $html[] = '<div class="row">';
            $html[] = '<div class="col-xs-12">';
            $html[] = '<div class="alert alert-warning">' . $message . '</div>';
            $html[] = '</div>';
            $html[] = '</div>';
        }

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12 col-md-4 col-lg-3">';
        $html[] = $this->renderMenu();
        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-md-8 col-lg-9">';
        $html[] = $this->renderTable();
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PROPERTY_CATEGORY;
        $additionalParameters[] = self::PARAM_WORKSPACE_ID;
        $additionalParameters[] = self::PARAM_IN_WORKSPACES;

        return parent::getAdditionalParameters($additionalParameters);
    }

    /**
     * @return ButtonToolBarRenderer
     */
    public function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());

            if ($this->isInWorkspaces())
            {
                $translator = Translation::getInstance();
                $translationContext = Manager::CONTEXT;

                $button = new DropdownButton(
                    $translator->getTranslation(
                        'CurrentWorkspace', ['WORKSPACE' => $this->getCurrentWorkspace()->getTitle()],
                        $translationContext
                    )
                );

                $workspaces = $this->getWorkspacesForUser();

                foreach ($workspaces as $workspace)
                {
                    $isActive = $workspace->getId() == $this->getCurrentWorkspace()->getId();

                    $button->addSubButton(
                        new SubButton(
                            $workspace->getTitle(), null,
                            $this->get_url([self::PARAM_WORKSPACE_ID => $workspace->getId()]), SubButton::DISPLAY_LABEL,
                            null, [], null, $isActive
                        )
                    );
                }

                $buttonToolbar->addItem($button);
            }

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /*
     * Inherited
     */

    /**
     * Returns the selected category id
     *
     * @return int
     */
    protected function getCategoryId()
    {
        $categoryId = $this->filterData->get_category();

        return $categoryId ?: 0;
    }

    public function getContentObjectTableRenderer(): ContentObjectTableRenderer
    {
        return $this->getService(ContentObjectTableRenderer::class);
    }

    /**
     * Returns the previously setup filterdata
     *
     * @return FilterData
     */
    public function getFilterData()
    {
        return $this->filterData;
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    public function getRightsService(): RightsService
    {
        return $this->getService(RightsService::class);
    }

    public function getWorkspaceContentObjectService(): WorkspaceContentObjectService
    {
        return $this->getService(WorkspaceContentObjectService::class);
    }

    public function getWorkspaceService(): WorkspaceService
    {
        return $this->getService(WorkspaceService::class);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     */
    protected function getWorkspacesForUser()
    {
        return $this->getWorkspaceService()->getWorkspacesForUser(
            $this->getUser(), RightsService::RIGHT_USE, null, null, new OrderBy(
                [new OrderProperty(new PropertyConditionVariable(Workspace::class, Workspace::PROPERTY_NAME))]
            )
        );
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_table_condition()
     */

    /**
     * @param int $category_id
     *
     * @return string
     */
    public function get_category_url($category_id)
    {
        return $this->get_url([self::PROPERTY_CATEGORY => $category_id], [self::PARAM_QUERY]);
    }

    /**
     * @param bool $allow_shared
     *
     * @return \Chamilo\Core\Repository\Viewer\Menu\RepositoryCategoryMenu
     */
    public function renderMenu(bool $allow_shared = true): string
    {
        $url = $this->get_url($this->get_parameters(), [self::PARAM_QUERY]) . '&' . self::PROPERTY_CATEGORY . '=%s';

        $extra = [];

        $menu = new RepositoryCategoryMenu(
            $this, $this->get_user_id(), $this->getCurrentWorkspace(), $this->getCategoryId(), $url, $extra,
            $this->get_types()
        );

        return $menu->render_as_tree();
    }

    /**
     * @return string NULL
     */
    protected function get_query()
    {
        return $this->getButtonToolbarRenderer()->getSearchForm()->getQuery();
    }

    /**
     * @return bool
     */
    protected function isInWorkspaces()
    {
        return $this->getRequest()->query->get(self::PARAM_IN_WORKSPACES);
    }

    /**
     * Registers the query as parameter to be used in other links
     */
    protected function registerQuery()
    {
        $query = $this->get_query();
        $this->set_parameter(self::PARAM_QUERY, $query);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderTable(): string
    {
        $contentObjectService = $this->getWorkspaceContentObjectService();
        $filterData = $this->getFilterData();
        $workspace = $this->getCurrentWorkspace();

        $totalNumberOfItems = $contentObjectService->countContentObjectsByTypeForWorkspace(
            $filterData->getTypeDataClass(), $workspace, new ConditionFilterRenderer(
                $filterData, $workspace
            )
        );

        $contentObjectTableRenderer = $this->getContentObjectTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $contentObjectTableRenderer->getParameterNames(), $contentObjectTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $contentObjects = $contentObjectService->getContentObjectsByTypeForWorkspace(
            $filterData->getTypeDataClass(), $workspace, new ConditionFilterRenderer(
            $filterData, $workspace
        ), $tableParameterValues->getNumberOfItemsPerPage(), $tableParameterValues->getOffset(),
            $contentObjectTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $contentObjectTableRenderer->render($tableParameterValues, $contentObjects);
    }

    /**
     * Setup the selected parameters in the repository filter data
     */
    protected function setupFilterData()
    {
        $filterData = new FilterData($this->getCurrentWorkspace());
        $filterData->set_filter_property(FilterData::FILTER_TEXT, $this->get_query());

        $typeSelectorFactory = new TypeSelectorFactory($this->get_types(), $this->getUser()->getId());
        $type_selector = $typeSelectorFactory->getTypeSelector();

        $all_types = $type_selector->get_unique_content_object_template_ids();

        $type_selection = $this->getSelectedTypes();

        if ($type_selection)
        {
            $types = [$type_selection];
            $types = array_intersect($types, $all_types);
        }
        else
        {
            $types = $all_types;
        }

        if (count($types) == 1)
        {
            $types = $types[0];
        }

        $filterData->set_filter_property(FilterData::FILTER_TYPE, $types);
        $filterData->setExcludedContentObjectIds($this->get_excluded_objects());

        $this->filterData = $filterData;
    }
}
