<?php
namespace Chamilo\Core\Repository;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\Renderer\FormFilterRenderer;
use Chamilo\Core\Repository\Filter\Renderer\HtmlFilterRenderer;
use Chamilo\Core\Repository\Menu\ObjectTypeMenu;
use Chamilo\Core\Repository\Menu\RepositoryCategoryTreeMenu;
use Chamilo\Core\Repository\Menu\RepositoryMenu;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregator;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Service\WorkspaceExtensionManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\UserView\Menu\UserViewMenu;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\BreadcrumbGeneratorInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\ContentTab;
use Chamilo\Libraries\Format\Tabs\GenericTabsRenderer;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Format\Tabs\TabsRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository
 * @author  Bart Mollet
 * @author  Tim De Pauw
 * @author  Dieter De Neef
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_BROWSE_CONTENT_OBJECTS = 'Browser';
    public const ACTION_BROWSE_RECYCLED_CONTENT_OBJECTS = 'RecycleBinBrowser';
    public const ACTION_BROWSE_SHARED_CONTENT_OBJECTS = 'SharedContentObjectsBrowser';
    public const ACTION_BUILD_COMPLEX_CONTENT_OBJECT = 'Builder';
    public const ACTION_COMPARE_CONTENT_OBJECTS = 'Comparer';
    public const ACTION_COPY_CONTENT_OBJECT = 'Copier';
    public const ACTION_CREATE_CONTENT_OBJECTS = 'Creator';
    public const ACTION_DELETE_CONTENT_OBJECTS = 'Deleter';
    public const ACTION_DELETE_LINK = 'LinkDeleter';
    public const ACTION_DELETE_SHARED_CONTENT_OBJECTS = 'SharedContentObjectsDeleter';
    public const ACTION_DOWNLOAD_DOCUMENT = 'DocumentDownloader';
    public const ACTION_EDIT_CONTENT_OBJECTS = 'Editor';
    public const ACTION_EXPORT_CONTENT_OBJECTS = 'Exporter';
    public const ACTION_EXTENSION_LAUNCHER = 'ExtensionLauncher';
    public const ACTION_HTML_EDITOR_FILE = 'HtmlEditorFile';
    public const ACTION_IMPACT_VIEW_RECYCLE = 'ImpactViewRecycler';
    public const ACTION_IMPORT_CONTENT_OBJECTS = 'Importer';
    public const ACTION_LINK_CONTENT_OBJECT_ALTERNATIVE = 'AlternativeLinker';
    public const ACTION_LINK_CONTENT_OBJECT_METADATA_ELEMENT = 'MetadataElementLinker';
    public const ACTION_LINK_CONTENT_OBJECT_PROPERTY_METADATA = 'PropertyMetadataLinker';
    public const ACTION_LINK_PROVIDERS = 'ProviderLinker';
    public const ACTION_LINK_SCHEMAS = 'SchemaLinker';
    public const ACTION_MANAGE_CATEGORIES = 'CategoryManager';
    public const ACTION_MOVE_CONTENT_OBJECTS = 'Mover';
    public const ACTION_MOVE_SHARED_CONTENT_OBJECTS = 'SharedContentObjectsMover';
    public const ACTION_PREVIEW = 'Previewer';
    public const ACTION_PUBLICATION = 'Publication';
    public const ACTION_QUOTA = 'Quota';
    public const ACTION_REPOSITORY_VIEWER = 'RepositoryViewer';
    public const ACTION_RESTORE_CONTENT_OBJECTS = 'Restorer';
    public const ACTION_REVERT_CONTENT_OBJECTS = 'Reverter';
    public const ACTION_UNLINK_CONTENT_OBJECTS = 'Unlinker';
    public const ACTION_USER_VIEW = 'UserView';
    public const ACTION_VIEW_ATTACHMENT = 'AttachmentViewer';
    public const ACTION_VIEW_CONTENT_OBJECTS = 'Viewer';
    public const ACTION_VIEW_DOUBLES = 'DoublesViewer';
    public const ACTION_VIEW_REPO = 'AttachmentViewer';
    public const ACTION_WORKSPACE = 'Workspace';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BROWSE_CONTENT_OBJECTS;

    public const PARAM_ADD_OBJECTS = 'add_objects';
    public const PARAM_ATTACHMENT_ID = 'attachment';
    public const PARAM_CATEGORY_ID = 'category';
    public const PARAM_CATEGORY_TYPE = 'category_type';
    public const PARAM_CLOI_COMPLEX_REF = 'cloi_complex_ref';
    public const PARAM_CLOI_ID = 'cloi_id';
    public const PARAM_CLOI_REF = 'cloi_ref';
    public const PARAM_CLOI_ROOT_ID = 'cloi_root_id';
    public const PARAM_COMPARE_OBJECT = 'object';
    public const PARAM_COMPARE_VERSION = 'compare';
    public const PARAM_CONTENT_OBJECT_ID = 'object';
    public const PARAM_CONTENT_OBJECT_MANAGER_TYPE = 'manage';
    public const PARAM_CONTENT_OBJECT_TEMPLATE_REGISTRATION_ID = 'template_id';
    public const PARAM_CONTENT_OBJECT_TYPE = 'content_object_type';
    public const PARAM_COPY_TO_TEMPLATES = 'copy_to_template';
    public const PARAM_DELETE_PERMANENTLY = 'delete_permanently';
    public const PARAM_DELETE_RECYCLED = 'delete_recycle';
    public const PARAM_DELETE_SELECTED = 'delete_selected';
    public const PARAM_DELETE_VERSION = 'delete_version';
    public const PARAM_DESTINATION_CONTENT_OBJECT_ID = 'destination';
    public const PARAM_DIRECTION_DOWN = 'down';
    public const PARAM_DIRECTION_UP = 'up';
    public const PARAM_DISPLAY_ORDER = 'display_order';
    public const PARAM_EDIT_SELECTED_RIGHTS = 'rights_selected';
    public const PARAM_EMPTY_RECYCLE_BIN = 'empty';
    public const PARAM_EXPORT_CP_SELECTED = 'export_cp_selected';
    public const PARAM_EXPORT_SELECTED = 'export_selected';
    public const PARAM_EXPORT_TYPE = 'export_type';
    public const PARAM_EXTERNAL_INSTANCE = 'external_instance';
    public const PARAM_EXTERNAL_OBJECT_ID = 'external_object_id';
    public const PARAM_EXTERNAL_REPOSITORY_ID = 'ext_rep_id';
    public const PARAM_IDENTIFIER = 'identifier';
    public const PARAM_IMPORT_TYPE = 'import_type';
    public const PARAM_LINK_ID = 'link_id';
    public const PARAM_LINK_TYPE = 'link_type';
    public const PARAM_MOVE_DIRECTION = 'move_direction';
    public const PARAM_MOVE_SELECTED = 'move_selected';
    public const PARAM_PUBLISH_SELECTED = 'publish_selected';
    public const PARAM_RECYCLE_SELECTED = 'recycle_selected';
    public const PARAM_REMOVE_SELECTED_CLOI = 'cloi_delete_selected';
    public const PARAM_RENAME = 'rename_co';
    public const PARAM_RENDERER = 'renderer';
    public const PARAM_RESTORE_SELECTED = 'restore_selected';
    public const PARAM_SHARED_CATEGORY_ID = 'shared_category_id';
    public const PARAM_SHARED_VIEW = 'shared_view';
    public const PARAM_SHOW_OBJECTS_SHARED_BY_ME = 'show_my_objects';
    public const PARAM_TARGET_GROUP = 'target_group';
    public const PARAM_TARGET_USER = 'target_user';
    public const PARAM_TYPE = 'type';
    public const PARAM_WORKSPACE_ID = 'workspace_id';

    public const SHARED_VIEW_ALL_OBJECTS = 2;
    public const SHARED_VIEW_OTHERS_OBJECTS = 0;
    public const SHARED_VIEW_OWN_OBJECTS = 1;

    public const TABS_CONTENT_OBJECT = 'content_object';
    public const TABS_FILTER = 'advanced_filter';
    public const TAB_CATEGORY = 'Category';
    public const TAB_OBJECT_TYPE = 'ObjectType';
    public const TAB_SEARCH = 'Search';
    public const TAB_USERVIEW = 'Userview';

    private $category_menu;

    private Workspace $currentWorkspace;

    private $search_form;

    /**
     * Property of this repository manager.
     */
    private $search_parameters;

    /**
     * Constructor
     *
     * @param $user_id int The user id of current user
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        if ($this->getUser() && $this->getRequest()->getFromRequestOrQuery(self::PARAM_ACTION) != self::ACTION_DOWNLOAD_DOCUMENT)
        {
            $this->checkAuthorization(Manager::CONTEXT);
        }

        $this->set_optional_parameters();
    }

    public function count_categories($conditions = null)
    {
        return DataManager::count(RepositoryCategory::class, new DataClassCountParameters($conditions));
    }

    /**
     * Sets the active URL in the navigation menu.
     *
     * @param $url string The active URL.
     */
    public function force_menu_url($url)
    {
        $this->get_category_menu()->forceCurrentUrl($url);
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_WORKSPACE_ID;

        return parent::getAdditionalParameters($additionalParameters);
    }

    /**
     * @return \Chamilo\Core\Metadata\Entity\DataClassEntityFactory
     */
    public function getDataClassEntityFactory()
    {
        return $this->getService(DataClassEntityFactory::class);
    }

    /**
     * @return \Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface
     */
    public function getPublicationAggregator()
    {
        return $this->getService(PublicationAggregator::class);
    }

    public function getTabsRenderer(): TabsRenderer
    {
        return $this->getService(TabsRenderer::class);
    }

    public function getWorkspace(): Workspace
    {
        return $this->getService('Chamilo\Core\Repository\CurrentWorkspace');
    }

    public function getWorkspaceExtensionManager(): WorkspaceExtensionManager
    {
        return $this->getService(WorkspaceExtensionManager::class);
    }

    protected function getWorkspaceRightsService(): RightsService
    {
        return $this->getService(RightsService::class);
    }

    public function getWorkspaceService(): WorkspaceService
    {
        return $this->getService(WorkspaceService::class);
    }

    /**
     * @return string[]
     */
    public function get_allowed_content_object_types()
    {
        $types = DataManager::get_registered_types(true);

        foreach ($types as $index => $type)
        {
            $context = ClassnameUtilities::getInstance()->getNamespaceParent($type, 3);

            if (!Configuration::getInstance()->isRegisteredAndActive($context))
            {
                unset($types[$index]);
            }
        }

        return $types;
    }

    public function get_breadcrumb_generator(): BreadcrumbGeneratorInterface
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $object
     *
     * @return string
     */
    public function get_browse_complex_content_object_url($object)
    {
        return $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_BUILD_COMPLEX_CONTENT_OBJECT,
                self::PARAM_CONTENT_OBJECT_ID => $object->getId()
            ]
        );
    }

    /**
     * Gets the category menu.
     * This menu contains all categories in the repository of the current user. Additionally
     * some menu items are added - Recycle Bin - Create a new object - Quota - Search Results (ony if search is
     * performed)
     *
     * @param $force_search bool Whether the user is searching. If true, overrides the default, which is to request
     *                      this information from the search form.
     *
     * @return \Chamilo\Core\Repository\Menu\ContentObjectCategoryMenu The menu
     */
    private function get_category_menu($force_search = false)
    {
        $this->set_parameter(GenericTabsRenderer::PARAM_SELECTED_TAB, [self::TABS_FILTER => self::TAB_CATEGORY]);

        if (!isset($this->category_menu))
        {
            if ($force_search)
            {
                $search_url = '#';
            }
            else
            {
                $search_url = null;
            }

            $this->category_menu = new RepositoryCategoryTreeMenu($this->getWorkspace(), $this);

            if (isset($search_url))
            {
                $this->category_menu->forceCurrentUrl($search_url, true);
            }

            if ($this->get_action() != self::ACTION_BROWSE_CONTENT_OBJECTS)
            {
                $this->category_menu->forceCurrentUrl($this->get_url());
            }
        }

        return $this->category_menu;
    }

    public function get_content_object_alternative_linker($content_object)
    {
        return $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_LINK_CONTENT_OBJECT_ALTERNATIVE,
                self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id()
            ]
        );
    }

    /**
     * Gets the url to delete a object from recycle bin.
     *
     * @param $content_object ContentObject The object.
     *
     * @return string The requested URL.
     */
    public function get_content_object_deletion_url($content_object, $type = null)
    {
        if (!DataManager::content_object_deletion_allowed($content_object, $type))
        {
            return null;
        }

        if (isset($type))
        {
            $param = self::PARAM_DELETE_VERSION;
        }
        else
        {
            if ($content_object->get_state() == ContentObject::STATE_RECYCLED)
            {
                $param = self::PARAM_DELETE_PERMANENTLY;
            }
            else
            {
                $param = self::PARAM_DELETE_RECYCLED;
            }
        }

        return $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_DELETE_CONTENT_OBJECTS,
                self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id(),
                $param => 1
            ]
        );
    }

    public function get_content_object_display_attachment_url($attachment)
    {
        $parameters = $this->get_parameters();
        $parameters[self::PARAM_ATTACHMENT_ID] = $attachment->get_id();
        $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_ATTACHMENT;

        return $this->get_url($parameters);
    }

    /**
     * Gets the url to view a object.
     *
     * @param $content_object ContentObject The object.
     *
     * @return string The requested URL.
     */
    public function get_content_object_editing_url($content_object)
    {
        return $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_EDIT_CONTENT_OBJECTS,
                self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id()
            ]
        );
    }

    public function get_content_object_exporting_url($content_object, $type = null)
    {
        return $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_EXPORT_CONTENT_OBJECTS,
                self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id(),
                self::PARAM_EXPORT_TYPE => $type
            ], [self::PARAM_CATEGORY_ID]
        );
    }

    /**
     * Gets the url to move a object to another category.
     *
     * @param $content_object ContentObject The object.
     *
     * @return string The requested URL.
     */
    public function get_content_object_moving_url($content_object)
    {
        return $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_MOVE_CONTENT_OBJECTS,
                self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id()
            ]
        );
    }

    /**
     * Gets the url to recycle a object (move the object to the recycle bin).
     *
     * @param $content_object ContentObject The object.
     *
     * @return string The requested URL.
     */
    public function get_content_object_recycling_url($content_object, $force = false)
    {
        return $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_IMPACT_VIEW_RECYCLE,
                self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id()
            ]
        );
    }

    /**
     * Gets the url to restore a object from the recycle bin.
     *
     * @param $content_object ContentObject The object.
     *
     * @return string The requested URL.
     */
    public function get_content_object_restoring_url($content_object)
    {
        if ($content_object->get_state() != ContentObject::STATE_RECYCLED)
        {
            return null;
        }

        return $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_RESTORE_CONTENT_OBJECTS,
                self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id()
            ]
        );
    }

    /**
     * Gets the url to revert to a object version.
     *
     * @param $content_object ContentObject The object.
     *
     * @return string The requested URL.
     */
    public function get_content_object_revert_url($content_object)
    {
        if (!DataManager::content_object_revert_allowed($content_object))
        {
            return null;
        }

        return $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_REVERT_CONTENT_OBJECTS,
                self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id()
            ]
        );
    }

    /**
     * @param string $content_object_type
     * @param string $template_registration_id
     *
     * @return string
     */
    public function get_content_object_type_creation_url($template_registration_id)
    {
        return $this->get_url([TypeSelector::PARAM_SELECTION => $template_registration_id]);
    }

    /**
     * Gets the url to view a object.
     *
     * @param $content_object ContentObject The object.
     *
     * @return string The requested URL.
     */
    public function get_content_object_viewing_url($content_object)
    {
        if ($content_object->get_state() == ContentObject::STATE_RECYCLED)
        {
            return $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_VIEW_CONTENT_OBJECTS,
                    self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id(),
                    self::PARAM_CATEGORY_ID => null
                ]
            );
        }

        return $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_VIEW_CONTENT_OBJECTS,
                self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id(),
                FilterData::FILTER_CATEGORY => $content_object->get_parent_id()
            ]
        );
    }

    public function get_content_objects_exporting_url($type, $ids, $format)
    {
        return $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_EXPORT_CONTENT_OBJECTS,
                $type => $ids,
                self::PARAM_EXPORT_TYPE => $format
            ]
        );
    }

    /*
     * Returns the url of the attachment viewer. Used in rendition implementation @param ContentObject $attachment
     * @return string
     */

    public function get_copy_content_object_url($content_object_id)
    {
        return $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_COPY_CONTENT_OBJECT,
                self::PARAM_CONTENT_OBJECT_ID => $content_object_id
            ]
        );
    }

    public static function get_document_downloader_url($documentId, $securityCode = null)
    {
        if (!$securityCode)
        {
            $contentObject = DataManager::retrieve_by_id(
                ContentObject::class, $documentId
            );

            if ($contentObject)
            {
                $securityCode = $contentObject->calculate_security_code();
            }
            else
            {
                throw new ObjectNotExistException('ContentObject', $documentId);
            }
        }

        $redirect = new Redirect(
            [
                self::PARAM_CONTEXT => self::package(),
                self::PARAM_ACTION => self::ACTION_DOWNLOAD_DOCUMENT,
                self::PARAM_CONTENT_OBJECT_ID => $documentId,
                ContentObject::PARAM_SECURITY_CODE => $securityCode
            ]
        );

        return $redirect->getUrl();
    }

    public function get_menu(): string
    {
        $translator = Translation::getInstance();

        $html = [];

        $html[] = '<div id="repository-tree-container">';
        $tabs = new TabsCollection();

        $tabs->add(
            new ContentTab(
                self::TAB_CATEGORY, $translator->getTranslation('ViewCategoriesTab', null, Manager::CONTEXT),
                $this->get_category_menu()->render_as_tree(), new FontAwesomeGlyph('folder', ['fa-lg'], null, 'fas'),
                ContentTab::DISPLAY_ICON
            )
        );

        $filter_form = FormFilterRenderer::factory(
            FilterData::getInstance($this->getWorkspace()), $this->getWorkspace(), $this->get_user_id(),
            $this->get_allowed_content_object_types(), $this->get_url(
            [
                GenericTabsRenderer::PARAM_SELECTED_TAB => [self::TABS_FILTER => self::TAB_SEARCH],
                self::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS
            ], [self::PARAM_CATEGORY_ID]
        )
        );

        $tabs->add(
            new ContentTab(
                self::TAB_SEARCH, $translator->getTranslation('SearchTab', null, Manager::CONTEXT),
                $filter_form->render(), new FontAwesomeGlyph('search', ['fa-lg'], null, 'fas'), ContentTab::DISPLAY_ICON
            )
        );

        $selected_type = FilterData::getInstance($this->getWorkspace())->getType();
        $selected_category = FilterData::getInstance($this->getWorkspace())->get_type_category();

        $object_type = new ObjectTypeMenu(
            $this, $selected_type, $this->get_url(
            [
                GenericTabsRenderer::PARAM_SELECTED_TAB => [self::TABS_FILTER => self::TAB_OBJECT_TYPE],
                FilterData::FILTER_TYPE => '__SELECTION__',
                Application::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS
            ], [self::PARAM_CATEGORY_ID, self::PARAM_CONTENT_OBJECT_ID]
        ), $selected_category, $this->get_url(
            [
                GenericTabsRenderer::PARAM_SELECTED_TAB => [self::TABS_FILTER => self::TAB_OBJECT_TYPE],
                FilterData::FILTER_TYPE => '__CATEGORY__',
                Application::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS
            ], [self::PARAM_CATEGORY_ID, self::PARAM_CONTENT_OBJECT_ID]
        )
        );

        $tabs->add(
            new ContentTab(
                self::TAB_OBJECT_TYPE, $translator->getTranslation('TypeTab', null, Manager::CONTEXT),
                $object_type->render_as_tree(), new FontAwesomeGlyph('filter', ['fa-lg'], null, 'fas'),
                ContentTab::DISPLAY_ICON
            )
        );

        $current_user_view_id = FilterData::getInstance($this->getWorkspace())->get_user_view();
        $user_view = new UserViewMenu(
            $this, $current_user_view_id, $this->get_url(
            [
                FilterData::FILTER_USER_VIEW => '__VIEW__',
                GenericTabsRenderer::PARAM_SELECTED_TAB => [self::TABS_FILTER => self::TAB_USERVIEW]
            ]
        )
        );
        $tabs->add(
            new ContentTab(
                self::TAB_USERVIEW, $translator->getTranslation('UserViewTab', null, Manager::CONTEXT),
                $user_view->render_as_tree(), new FontAwesomeGlyph('object-group', ['fa-lg'], null, 'far'),
                ContentTab::DISPLAY_ICON
            )
        );

        $html[] = $this->getTabsRenderer()->render(self::TABS_FILTER, $tabs);

        $html[] = '</div>';

        $html_filter_renderer = HtmlFilterRenderer::factory(
            FilterData::getInstance($this->getWorkspace()), $this->getWorkspace()
        );

        $html[] = $html_filter_renderer->render();

        $repositoryMenu = new RepositoryMenu($this, $this->getWorkspaceRightsService());
        $html[] = $repositoryMenu->render();

        return implode(PHP_EOL, $html);
    }

    public function get_parameters(bool $include_search = false): array
    {
        if ($include_search && isset($this->search_parameters))
        {
            return array_merge($this->search_parameters, parent::get_parameters());
        }

        return parent::get_parameters();
    }

    public static function get_preview_content_object_url($content_object)
    {
        return Preview\Manager::get_content_object_default_action_link($content_object);
    }

    public function get_publish_content_object_url($content_object)
    {
        return $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_PUBLICATION,
                Publication\Manager::PARAM_ACTION => Publication\Manager::ACTION_PUBLISH,
                self::PARAM_CONTENT_OBJECT_ID => $content_object->get_id()
            ]
        );
    }

    /**
     * Gets the URL to the recycle bin.
     *
     * @return string The URL.
     */
    public function get_recycle_bin_url()
    {
        return $this->get_url(
            [self::PARAM_ACTION => self::ACTION_BROWSE_RECYCLED_CONTENT_OBJECTS, self::PARAM_CATEGORY_ID => null]
        );
    }

    /**
     * Gets the url for browsing objects of a given type
     *
     * @param int $template_registration_id
     *
     * @return string The url
     */
    public function get_type_filter_url($template_registration_id)
    {
        $params = [];
        $params[self::PARAM_ACTION] = self::ACTION_BROWSE_CONTENT_OBJECTS;
        $params[FilterData::FILTER_TYPE] = $template_registration_id;

        return $this->get_url($params);
    }

    public function has_menu(): bool
    {
        return true;
    }

    public function render_header(string $pageTitle = ''): string
    {
        $html = [];

        $html[] = parent::render_header($pageTitle);

        if ($this->get_action() == self::ACTION_HTML_EDITOR_FILE)
        {
            return implode(PHP_EOL, $html);
        }

        /** @var Workspace $workspace */
        $workspace = $this->getWorkspace();

        $html[] = '<div class="alert alert-warning" style="font-size: 12px; font-weight: bold;">';
        $html[] = Translation::getInstance()->get(
            'CurrentlyWorkingInWorkspace', ['TITLE' => $workspace->getTitle()]
        );

        if ($workspace->getDescription())
        {
            $html[] = '<div style="font-weight: normal; margin-top: 15px; margin-bottom: -10px;">' .
                $workspace->getDescription() . '</div>';
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function set_optional_parameters()
    {
        $this->set_parameter(
            GenericTabsRenderer::PARAM_SELECTED_TAB, Request::get(GenericTabsRenderer::PARAM_SELECTED_TAB)
        );
    }
}
