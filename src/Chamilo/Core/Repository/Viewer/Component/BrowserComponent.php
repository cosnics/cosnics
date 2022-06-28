<?php
namespace Chamilo\Core\Repository\Viewer\Component;

use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Selector\TypeSelectorFactory;
use Chamilo\Core\Repository\Viewer\Filter\FilterData;
use Chamilo\Core\Repository\Viewer\Manager;
use Chamilo\Core\Repository\Viewer\Menu\RepositoryCategoryMenu;
use Chamilo\Core\Repository\Viewer\Table\ContentObject\ContentObjectTable;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class BrowserComponent extends Manager implements TableSupport
{
    public const PROPERTY_CATEGORY = 'category';

    public const SHARED_BROWSER = 'shared';

    public const SHARED_BROWSER_ALLOWED = 'allow_shared_browser';

    /**
     *
     * @var ButtonToolBarRenderer
     */
    protected $buttonToolbarRenderer;

    /**
     *
     * @var FilterData
     */
    protected $filterData;

    /**
     *
     * @var WorkspaceInterface
     */
    protected $workspace;

    /**
     *
     * @var WorkspaceService
     */
    protected $workspaceService;

    public function run()
    {
        $this->checkAuthorization(\Chamilo\Core\Repository\Manager::context());

        $this->workspaceService = new WorkspaceService(new WorkspaceRepository());
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

        $menu = $this->get_menu();
        $table = $this->get_object_table();

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12 col-md-4 col-lg-3">';
        $html[] = $menu->render_as_tree();
        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-md-8 col-lg-9">';
        $html[] = $table->as_html();
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /*
     * Inherited
     */

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PROPERTY_CATEGORY;
        $additionalParameters[] = self::PARAM_WORKSPACE_ID;
        $additionalParameters[] = self::PARAM_IN_WORKSPACES;

        return parent::getAdditionalParameters($additionalParameters);
    }

    /**
     *
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
                $translationContext = Manager::context();

                $button = new DropdownButton(
                    $translator->getTranslation(
                        'CurrentWorkspace', array('WORKSPACE' => $this->getWorkspace()->getTitle()), $translationContext
                    )
                );

                $workspaces = $this->getWorkspacesForUser();

                foreach ($workspaces as $workspace)
                {
                    $isActive = $workspace->getId() == $this->getWorkspace()->getId();

                    $button->addSubButton(
                        new SubButton(
                            $workspace->getTitle(), null,
                            $this->get_url(array(self::PARAM_WORKSPACE_ID => $workspace->getId())),
                            SubButton::DISPLAY_LABEL, null, [], null, $isActive
                        )
                    );
                }

                $buttonToolbar->addItem($button);
            }

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

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

    /**
     * Returns the previously setup filterdata
     *
     * @return FilterData
     */
    public function getFilterData()
    {
        return $this->filterData;
    }

    /**
     *
     * @return WorkspaceInterface
     */
    public function getWorkspace()
    {
        if (!isset($this->workspace))
        {
            if ($this->isInWorkspaces())
            {

                $identifier = $this->getRequest()->query->get(self::PARAM_WORKSPACE_ID);
                $workspace = $this->workspaceService->getWorkspaceByIdentifier($identifier);

                if (!$workspace)
                {
                    $workspaces = $this->getWorkspacesForUser();
                    $workspace = $workspaces->current();

                    if (!$workspace)
                    {
                        throw new UserException(
                            Translation::getInstance()->getTranslation(
                                'NoValidWorkspacesForUser', null, Manager::context()
                            )
                        );
                    }
                }

                $this->workspace = $workspace;
            }
            else
            {
                $this->workspace = new PersonalWorkspace($this->getUser());
            }
        }

        return $this->workspace;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     */
    protected function getWorkspacesForUser()
    {
        return $this->workspaceService->getWorkspacesForUser(
            $this->getUser(), RightsService::RIGHT_USE, null, null, new OrderBy(
                array(new OrderProperty(new PropertyConditionVariable(Workspace::class, Workspace::PROPERTY_NAME)))
            )
        );
    }

    /**
     *
     * @param int $category_id
     *
     * @return string
     */
    public function get_category_url($category_id)
    {
        return $this->get_url(array(self::PROPERTY_CATEGORY => $category_id), array(self::PARAM_QUERY));
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $content_object
     *
     * @return \Chamilo\Libraries\Format\Structure\Toolbar
     */
    public function get_default_browser_actions($content_object)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        if (RightsService::getInstance()->canUseContentObject($this->get_user(), $content_object))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Publish', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('share-square'),
                    $this->get_url(
                        array_merge($this->get_parameters(), array(self::PARAM_ID => $content_object->get_id())), false
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if (RightsService::getInstance()->canViewContentObject($this->get_user(), $content_object))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Preview'), new FontAwesomeGlyph('desktop'), $this->get_url(
                    array_merge(
                        $this->get_parameters(), array(
                            self::PARAM_TAB => self::TAB_VIEWER,
                            self::PARAM_ACTION => self::ACTION_VIEWER,
                            self::PARAM_VIEW_ID => $content_object->get_id()
                        )
                    ), false
                ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if (RightsService::getInstance()->canEditContentObject($this->get_user(), $content_object) &&
            RightsService::getInstance()->canUseContentObject($this->get_user(), $content_object))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('EditAndPublish'), new FontAwesomeGlyph('edit'), $this->get_url(
                    array_merge(
                        $this->get_parameters(), array(
                            self::PARAM_TAB => self::TAB_CREATOR,
                            self::PARAM_ACTION => self::ACTION_CREATOR,
                            self::PARAM_EDIT_ID => $content_object->get_id()
                        )
                    ), false
                ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($content_object instanceof ComplexContentObjectSupport &&
            RightsService::getInstance()->canViewContentObject($this->get_user(), $content_object))
        {

            $preview_url = \Chamilo\Core\Repository\Manager::get_preview_content_object_url($content_object);
            $onclick = '" onclick="javascript:openPopup(\'' . addslashes($preview_url) . '\'); return false;';
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Preview', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('desktop'),
                    $preview_url, ToolbarItem::DISPLAY_ICON, false, $onclick, '_blank'
                )
            );
        }

        return $toolbar;
    }

    /**
     *
     * @param bool $allow_shared
     *
     * @return \Chamilo\Core\Repository\Viewer\Menu\RepositoryCategoryMenu
     */
    public function get_menu(bool $allow_shared = true): string
    {
        $url =
            $this->get_url($this->get_parameters(), array(self::PARAM_QUERY)) . '&' . self::PROPERTY_CATEGORY . '=%s';

        $extra = [];

        $menu = new RepositoryCategoryMenu(
            $this, $this->get_user_id(), $this->getWorkspace(), $this->getCategoryId(), $url, $extra, $this->get_types()
        );

        return $menu;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Viewer\Table\ContentObject\ContentObjectTable
     */
    protected function get_object_table()
    {
        return new ContentObjectTable($this);
    }

    /**
     *
     * @return string NULL
     */
    protected function get_query()
    {
        return $this->getButtonToolbarRenderer()->getSearchForm()->getQuery();
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_table_condition()
     */

    public function get_table_condition($table_class_name)
    {
    }

    /**
     *
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
     * Setup the selected parameters in the repository filter data
     */
    protected function setupFilterData()
    {
        $filterData = new FilterData($this->getWorkspace());
        $filterData->set_filter_property(FilterData::FILTER_TEXT, $this->get_query());

        $typeSelectorFactory = new TypeSelectorFactory($this->get_types(), $this->getUser()->getId());
        $type_selector = $typeSelectorFactory->getTypeSelector();

        $all_types = $type_selector->get_unique_content_object_template_ids();

        $type_selection = TypeSelector::get_selection();

        if ($type_selection)
        {
            $types = array($type_selection);
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
