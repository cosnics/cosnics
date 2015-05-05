<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Common\Export\ContentObjectExportImplementation;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Publication\PublicationInterface;
use Chamilo\Core\Repository\RepositoryRights;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Table\ContentObject\Version\VersionTable;
use Chamilo\Core\Repository\Table\ExternalLink\ExternalLinkTable;
use Chamilo\Core\Repository\Table\Link\LinkTable;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * $Id: viewer.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component
 */

/**
 * Repository manager component which can be used to view a learning object.
 */
class ViewerComponent extends Manager implements DelegateComponent, TableSupport
{

    private $action_bar;

    private $object;

    private $tabs;

    /**
     * caching variable that checks whether or not the current user can modify this object
     *
     * @var bool
     */
    private $allowed_to_modify;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $id = Request :: get(self :: PARAM_CONTENT_OBJECT_ID);

        if ($id)
        {
            $renderer_name = ClassnameUtilities :: getInstance()->getClassnameFromObject($this, true);
            $this->tabs = new DynamicTabsRenderer($renderer_name);

            $object = DataManager :: retrieve_by_id(ContentObject :: class_name(), $id);

            if (! $object)
            {
                return $this->display_error_page(
                    Translation :: get('NoObjectSelected', null, Utilities :: COMMON_LIBRARIES));
            }

            $this->object = $object;

            if (! ($object->get_owner_id() == $this->get_user_id() || $object->get_owner_id() == 0 || RepositoryRights :: get_instance()->is_allowed_in_user_subtree(
                RepositoryRights :: VIEW_RIGHT,
                $object->get_id(),
                RepositoryRights :: TYPE_USER_CONTENT_OBJECT,
                $object->get_owner_id())))
            {
                throw new NotAllowedException();
            }

            $is_owner = $object->get_owner_id() == $this->get_user_id();

            $this->allowed_to_modify = $is_owner || RepositoryRights :: get_instance()->is_allowed_in_user_subtree(
                RepositoryRights :: COLLABORATE_RIGHT,
                $object->get_id(),
                RepositoryRights :: TYPE_USER_CONTENT_OBJECT,
                $object->get_owner_id());

            $display = ContentObjectRenditionImplementation :: factory(
                $object,
                ContentObjectRendition :: FORMAT_HTML,
                ContentObjectRendition :: VIEW_FULL,
                $this);
            $trail = BreadcrumbTrail :: get_instance();
            BreadcrumbTrail :: get_instance()->add(
                new Breadcrumb(
                    null,
                    Translation :: get(
                        'ViewContentObject',
                        array(
                            'CONTENT_OBJECT' => $object->get_title(),
                            'ICON' => Theme :: getInstance()->getImage(
                                'Logo/16',
                                'png',
                                Translation :: get('TypeName', null, $object->package()),
                                null,
                                ToolbarItem :: DISPLAY_ICON,
                                false,
                                $object->package())),
                        self :: package())));

            if ($object->get_state() == ContentObject :: STATE_RECYCLED)
            {
                $trail->add(new Breadcrumb($this->get_recycle_bin_url(), Translation :: get('RecycleBin')));
                $this->force_menu_url($this->get_recycle_bin_url());
            }
            // $trail->add(new Breadcrumb($this->get_url(), $object->get_title()
            // . ($object->is_latest_version() ? '' : ' (' . Translation ::
            // get('OldVersion') . ')')));

            $version_data = array();
            $versions = $object->get_content_object_versions();

            $publication_attr = array();

            foreach ($object->get_content_object_versions() as $version)
            {
                // If this learning object is published somewhere in an
                // application, these locations are listed here.
                $publications = \Chamilo\Core\Repository\Publication\Storage\DataManager\DataManager :: get_content_object_publication_attributes(
                    $version->get_id(),
                    PublicationInterface :: ATTRIBUTES_TYPE_OBJECT);
                $publication_attr = array_merge($publication_attr, $publications->as_array());
            }

            $this->action_bar = $this->get_action_bar($object);

            $html = array();

            if (count($versions) >= 2)
            {
                $html[] = $this->render_header();

                if ($this->action_bar)
                {
                    $html[] = '<br />' . $this->action_bar->as_html();
                }

                $html[] = $display->render();

                $version_parameters = array(
                    self :: PARAM_CONTEXT => self :: context(),
                    self :: PARAM_CONTENT_OBJECT_ID => $this->object->get_id(),
                    self :: PARAM_ACTION => self :: ACTION_COMPARE_CONTENT_OBJECTS);

                $version_browser = new VersionTable($this);

                $version_tab_content = array();
                $version_tab_content[] = $version_browser->as_html();
                $version_tab_content[] = ResourceManager :: get_instance()->get_resource_html(
                    Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository', true) . 'Repository.js');
                $this->tabs->add_tab(
                    new DynamicContentTab(
                        'versions',
                        Translation :: get('Versions'),
                        Theme :: getInstance()->getImagePath('Chamilo\Core\Repository', 'PlaceMini/Versions'),
                        implode(PHP_EOL, $version_tab_content)));
            }
            elseif (count($publication_attr) > 0)
            {
                $html[] = $this->render_header();

                if ($this->action_bar)
                {
                    $html[] = '<br />' . $this->action_bar->as_html();
                }

                $html[] = $display->render();
            }
            else
            {
                $html[] = $this->render_header();

                if ($this->action_bar)
                {
                    $html[] = '<br />' . $this->action_bar->as_html();
                }
                $html[] = $display->render();
            }

            $this->add_links_to_content_object_tabs($object);

            $html[] = $this->tabs->render();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation :: get(
                        'NoObjectSelected',
                        array('OBJECT' => Translation :: get('ContentObject')),
                        Utilities :: COMMON_LIBRARIES)));
        }
    }

    private function get_action_bar($object)
    {
        $user = $this->get_user_info($this->get_user_id());
        $is_owner = $object->get_owner_id() == $this->get_user_id();

        if ($this->is_allowed_to_modify())
        {
            $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

            if ($object->is_latest_version())
            {
                $edit_url = $this->get_content_object_editing_url($object);
            }

            if (isset($edit_url))
            {
                $recycle_url = $this->get_content_object_recycling_url($object);
                $in_recycle_bin = false;

                if (isset($recycle_url) && $is_owner)
                {
                    $action_bar->add_common_action(
                        new ToolbarItem(
                            Translation :: get('Remove', null, Utilities :: COMMON_LIBRARIES),
                            Theme :: getInstance()->getCommonImagePath('Action/RecycleBin'),
                            $recycle_url,
                            ToolbarItem :: DISPLAY_ICON_AND_LABEL,
                            Translation :: get('ConfirmRemove', null, Utilities :: COMMON_LIBRARIES)));
                }
                else
                {
                    if ($is_owner)
                    {
                        $delete_url = $this->get_content_object_deletion_url($object);
                        if (isset($delete_url))
                        {
                            $recycle_bin_button = new ToolbarItem(
                                Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                                Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                                $delete_url,
                                ToolbarItem :: DISPLAY_ICON_AND_LABEL,
                                Translation :: get('ConfirmDelete', null, Utilities :: COMMON_LIBRARIES));
                            $in_recycle_bin = true;
                        }
                        else
                        {
                            $recycle_bin_button = new ToolbarItem(
                                Translation :: get('Remove', null, Utilities :: COMMON_LIBRARIES),
                                Theme :: getInstance()->getCommonImagePath('Action/RecycleBinNa'));
                        }
                    }
                }

                if (! $in_recycle_bin)
                {
                    $delete_link_url = $this->get_content_object_unlinker_url($object);

                    if (! DataManager :: content_object_deletion_allowed($object) &&
                         $object->get_state() != ContentObject :: STATE_RECYCLED && $is_owner)
                    {
                        $force_delete_button = new ToolbarItem(
                            Translation :: get('Unlink', null, Utilities :: COMMON_LIBRARIES),
                            Theme :: getInstance()->getCommonImagePath('Action/Unlink'),
                            $delete_link_url,
                            ToolbarItem :: DISPLAY_ICON_AND_LABEL,
                            true);
                    }

                    $edit_url = $this->get_content_object_editing_url($object);

                    if (isset($edit_url))
                    {
                        $action_bar->add_common_action(
                            new ToolbarItem(
                                Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                                Theme :: getInstance()->getCommonImagePath('Action/Edit'),
                                $edit_url,
                                ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                    }
                    else
                    {
                        $action_bar->add_common_action(
                            new ToolbarItem(
                                Translation :: get('EditNotAvailable', null, Utilities :: COMMON_LIBRARIES),
                                Theme :: getInstance()->getCommonImagePath('Action/EditNa')));
                    }

                    if (isset($recycle_bin_button) && $is_owner)
                    {
                        $action_bar->add_common_action($recycle_bin_button);
                    }

                    if (isset($force_delete_button) && $is_owner)
                    {
                        $action_bar->add_common_action($force_delete_button);
                    }

                    if (DataManager :: get_number_of_categories($user->get_id()) > 1 && $is_owner)
                    {
                        $move_url = $this->get_content_object_moving_url($object);
                        $action_bar->add_common_action(
                            new ToolbarItem(
                                Translation :: get('Move', null, Utilities :: COMMON_LIBRARIES),
                                Theme :: getInstance()->getCommonImagePath('Action/Move'),
                                $move_url,
                                ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                    }

                    if ($is_owner)
                    {
                        $hide_sharing = PlatformSetting :: get('hide_sharing', __NAMESPACE__) === 1 ? true : false;
                        if (! $hide_sharing)
                        {
                            $action_bar->add_common_action(
                                new ToolbarItem(
                                    Translation :: get('Share', null, Utilities :: COMMON_LIBRARIES),
                                    Theme :: getInstance()->getCommonImagePath('Action/Share'),
                                    $this->get_share_content_objects_url($object->get_id())));
                        }
                    }

                    if ($object instanceof ComplexContentObjectSupport)
                    {
                        if (\Chamilo\Core\Repository\Builder\Manager :: exists($object->package()))
                        {

                            $action_bar->add_common_action(
                                new ToolbarItem(
                                    Translation :: get('BuildComplexObject', null, Utilities :: COMMON_LIBRARIES),
                                    Theme :: getInstance()->getCommonImagePath('Action/Build'),
                                    $this->get_browse_complex_content_object_url($object),
                                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));

                            $preview_url = $this->get_preview_content_object_url($object);
                            $onclick = '" onclick="javascript:openPopup(\'' . $preview_url . '\'); return false;';
                            $action_bar->add_common_action(
                                new ToolbarItem(
                                    Translation :: get('Preview', null, Utilities :: COMMON_LIBRARIES),
                                    Theme :: getInstance()->getCommonImagePath('Action/Preview'),
                                    $preview_url,
                                    ToolbarItem :: DISPLAY_ICON_AND_LABEL,
                                    false,
                                    $onclick,
                                    '_blank'));
                        }
                        else
                        {

                            $preview_url = $this->get_preview_content_object_url($object);
                            $onclick = '" onclick="javascript:openPopup(\'' . $preview_url . '\'); return false;';
                            $action_bar->add_common_action(
                                new ToolbarItem(
                                    Translation :: get('BuildPreview', null, Utilities :: COMMON_LIBRARIES),
                                    Theme :: getInstance()->getCommonImagePath('Action/BuildPreview'),
                                    $preview_url,
                                    ToolbarItem :: DISPLAY_ICON_AND_LABEL,
                                    false,
                                    $onclick,
                                    '_blank'));
                        }
                    }
                }
                else
                {
                    if ($is_owner)
                    {
                        $restore_url = $this->get_content_object_restoring_url($object);
                        $action_bar->add_common_action(
                            new ToolbarItem(
                                Translation :: get('Restore', null, Utilities :: COMMON_LIBRARIES),
                                Theme :: getInstance()->getCommonImagePath('Action/Restore'),
                                $restore_url,
                                ToolbarItem :: DISPLAY_ICON_AND_LABEL,
                                true));
                        if (isset($recycle_bin_button))
                        {
                            $action_bar->add_common_action($recycle_bin_button);
                        }
                    }
                }
            }

            // TODO implement settings structure to allow templates: at the
            // moment quick fix to disallow non-admins
            if ($user->is_platform_admin())
            {
                $action_bar->add_tool_action(
                    new ToolbarItem(
                        Translation :: get('CopyToTemplates'),
                        Theme :: getInstance()->getCommonImagePath('Export/Template'),
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => Manager :: ACTION_TEMPLATE,
                                \Chamilo\Core\Repository\Template\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Template\Manager :: ACTION_CREATE))));
            }

            return $action_bar;
        }
    }

    public function add_links_to_content_object_tabs($content_object)
    {
        $renderer_name = ClassnameUtilities :: getInstance()->getClassnameFromObject($this, true);
        $tabs = new DynamicTabsRenderer($renderer_name);
        $parameters = array(
            self :: PARAM_CONTEXT => self :: context(),
            self :: PARAM_CONTENT_OBJECT_ID => $this->object->get_id(),
            self :: PARAM_ACTION => self :: ACTION_VIEW_CONTENT_OBJECTS);

        // EXTERNAL INSTANCES
        if ($content_object->is_external())
        {
            $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = 'external_instances';
            $browser = new ExternalLinkTable($this);
            $this->tabs->add_tab(
                new DynamicContentTab(
                    'external_instances',
                    Translation :: get('ExternalInstances'),
                    Theme :: getInstance()->getImagePath('Chamilo\Core\Repository', 'PlaceMini/ExternalInstance'),
                    $browser->as_html()));
        }

        // LINKS | PUBLICATIONS
        if ($content_object->has_publications())
        {
            $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = LinkTable :: TYPE_PUBLICATIONS;
            $browser = new LinkTable($this, LinkTable :: TYPE_PUBLICATIONS);
            $this->tabs->add_tab(
                new DynamicContentTab(
                    LinkTable :: TYPE_PUBLICATIONS,
                    Translation :: get('Publications'),
                    Theme :: getInstance()->getImagePath('Chamilo\Core\Repository', 'PlaceMini/Publications'),
                    $browser->as_html()));
        }
        // EXPORT
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = 'export';
        $this->tabs->add_tab(
            new DynamicContentTab(
                'export',
                Translation :: get('Export'),
                Theme :: getInstance()->getImagePath('Chamilo\Core\Repository', 'PlaceMini/Export'),
                $this->get_export_types()));

        // LINKS | PARENTS
        if ($content_object->has_parents())
        {
            $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = LinkTable :: TYPE_PARENTS;
            $browser = new LinkTable($this, LinkTable :: TYPE_PARENTS);
            $this->tabs->add_tab(
                new DynamicContentTab(
                    LinkTable :: TYPE_PARENTS,
                    Translation :: get('UsedIn'),
                    Theme :: getInstance()->getImagePath('Chamilo\Core\Repository', 'PlaceMini/Parents'),
                    $browser->as_html()));
        }

        // LINKS | CHILDREN
        if ($content_object->has_children())
        {
            $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = LinkTable :: TYPE_CHILDREN;
            $browser = new LinkTable($this, LinkTable :: TYPE_CHILDREN);
            $this->tabs->add_tab(
                new DynamicContentTab(
                    LinkTable :: TYPE_CHILDREN,
                    Translation :: get('Uses'),
                    Theme :: getInstance()->getImagePath('Chamilo\Core\Repository', 'PlaceMini/Children'),
                    $browser->as_html()));
        }

        // LINKS | ATTACHED TO
        if ($content_object->has_attachers())
        {
            $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = LinkTable :: TYPE_ATTACHED_TO;
            $browser = new LinkTable($this, LinkTable :: TYPE_ATTACHED_TO);
            $this->tabs->add_tab(
                new DynamicContentTab(
                    LinkTable :: TYPE_ATTACHED_TO,
                    Translation :: get('AttachedTo'),
                    Theme :: getInstance()->getImagePath('Chamilo\Core\Repository', 'PlaceMini/AttachedTo'),
                    $browser->as_html()));
        }

        // LINKS | ATTACHES
        if ($content_object->has_attachments())
        {
            $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = LinkTable :: TYPE_ATTACHES;
            $browser = new LinkTable($this, LinkTable :: TYPE_ATTACHES);
            $this->tabs->add_tab(
                new DynamicContentTab(
                    LinkTable :: TYPE_ATTACHES,
                    Translation :: get('Attaches'),
                    Theme :: getInstance()->getImagePath('Chamilo\Core\Repository', 'PlaceMini/Attaches'),
                    $browser->as_html()));
        }

        // LINKS | INCLUDED IN
        if ($content_object->has_includers())
        {
            $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = LinkTable :: TYPE_INCLUDED_IN;
            $browser = new LinkTable($this, LinkTable :: TYPE_INCLUDED_IN);
            $this->tabs->add_tab(
                new DynamicContentTab(
                    LinkTable :: TYPE_INCLUDED_IN,
                    Translation :: get('IncludedIn'),
                    Theme :: getInstance()->getImagePath('Chamilo\Core\Repository', 'PlaceMini/IncludedIn'),
                    $browser->as_html()));
        }

        // LINKS | INCLUDES
        if ($content_object->has_includes())
        {
            $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = LinkTable :: TYPE_INCLUDES;
            $browser = new LinkTable($this, LinkTable :: TYPE_INCLUDES);
            $this->tabs->add_tab(
                new DynamicContentTab(
                    LinkTable :: TYPE_INCLUDES,
                    Translation :: get('Includes'),
                    Theme :: getInstance()->getImagePath('Chamilo\Core\Repository', 'PlaceMini/Includes'),
                    $browser->as_html()));
        }
    }

    /**
     * Returns whether or not a user can change the links
     *
     * @return s bool
     */
    public function is_allowed_to_modify()
    {
        return $this->allowed_to_modify;
    }

    public function get_object()
    {
        return $this->object;
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        // $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
        // RepositoryManager :: PARAM_ACTION => RepositoryManager ::
        // ACTION_BROWSE_CONTENT_OBJECTS)), Translation ::
        // get('RepositoryManagerBrowserComponent')));
        $breadcrumbtrail->add_help('repository_viewer');
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_CONTENT_OBJECT_ID);
    }

    public function get_export_types()
    {
        $types = ContentObjectExportImplementation :: get_types_for_object(
            ClassnameUtilities :: getInstance()->getNamespaceFromObject($this->object));

        $html = array();
        foreach ($types as $type)
        {
            $link = $this->get_content_object_exporting_url($this->object, $type);
            $html[] = '<a href="' . $link . '">';
            $url = Theme :: getInstance()->getImagePath(
                ClassnameUtilities :: getInstance()->getNamespaceFromObject($this->object),
                'Export/' . $type,
                'png',
                false);

            if (file_exists($url))
            {
                $html[] = '<div class="create_block" style="background-image : url(' . Theme :: getInstance()->getImagePath(
                    ClassnameUtilities :: getInstance()->getNamespaceFromObject($this->object),
                    'Export/' . $type) . '); ">' . Translation :: get(
                    'ExportType' . StringUtilities :: getInstance()->createString($type)->upperCamelize(),
                    null,
                    ClassnameUtilities :: getInstance()->getNamespaceFromObject($this->object)) . '</div>';
            }
            else
            {
                $html[] = '<div class="create_block" style="background-image : url(' . Theme :: getInstance()->getImagePath(
                    'Chamilo\Core\Repository',
                    'Export/' . $type) . '); ">' . Translation :: get(
                    'ExportType' . StringUtilities :: getInstance()->createString($type)->upperCamelize()) . '</div>';
            }

            $html[] = '</a>';
        }
        return implode(PHP_EOL, $html);
    }

    public function get_table_condition($table_class_name)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OBJECT_NUMBER),
            new StaticConditionVariable($this->object->get_object_number()));
    }
}
