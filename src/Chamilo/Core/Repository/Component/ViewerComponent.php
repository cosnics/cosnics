<?php

namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Common\Export\ContentObjectExportImplementation;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Table\ContentObject\Version\VersionTable;
use Chamilo\Core\Repository\Table\ExternalLink\ExternalLinkTable;
use Chamilo\Core\Repository\Table\Link\LinkTable;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Table\SharedIn\SharedInTable;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package repository.lib.repository_manager.component
 */

/**
 * Repository manager component which can be used to view a learning object.
 */
class ViewerComponent extends Manager implements DelegateComponent, TableSupport
{

    private $object;

    private $tabs;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $id = Request::get(self::PARAM_CONTENT_OBJECT_ID);
        $this->set_parameter(self::PARAM_CONTENT_OBJECT_ID, $id);

        if ($id)
        {
            $renderer_name = ClassnameUtilities::getInstance()->getClassnameFromObject($this, true);
            $this->tabs = new DynamicTabsRenderer($renderer_name);

            $object = DataManager::retrieve_by_id(ContentObject::class_name(), $id);

            if (!$object)
            {
                return $this->display_error_page(
                    Translation::get('NoObjectSelected', null, Utilities::COMMON_LIBRARIES)
                );
            }

            $this->object = $object;

            if (!RightsService::getInstance()->canViewContentObject(
                $this->get_user(),
                $this->object,
                $this->getWorkspace()
            ))
            {
                throw new NotAllowedException();
            }

            $this->allowed_to_modify = RightsService::getInstance()->canEditContentObject(
                    $this->get_user(),
                    $this->object,
                    $this->getWorkspace()
                ) && \Chamilo\Core\Repository\Publication\Storage\DataManager\DataManager::is_content_object_editable(
                    $this->object->getId()
                );

            $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer($this->object);

            $display = ContentObjectRenditionImplementation::factory(
                $object,
                ContentObjectRendition::FORMAT_HTML,
                ContentObjectRendition::VIEW_FULL,
                $this
            );
            $trail = BreadcrumbTrail::getInstance();

            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb(
                    null,
                    Translation::get(
                        'ViewContentObject',
                        array(
                            'CONTENT_OBJECT' => $object->get_title(),
                            'ICON' => Theme::getInstance()->getImage(
                                'Logo/16',
                                'png',
                                Translation::get('TypeName', null, $object->package()),
                                null,
                                ToolbarItem::DISPLAY_ICON,
                                false,
                                $object->package()
                            )
                        ),
                        self::package()
                    )
                )
            );

            if ($object->get_state() == ContentObject::STATE_RECYCLED)
            {
                $trail->add(new Breadcrumb($this->get_recycle_bin_url(), Translation::get('RecycleBin')));
                $this->force_menu_url($this->get_recycle_bin_url());
            }

            if ($object->get_current() != ContentObject::CURRENT_SINGLE)
            {
                $version_parameters = array(
                    self::PARAM_CONTEXT => self::context(),
                    self::PARAM_CONTENT_OBJECT_ID => $this->object->get_id(),
                    self::PARAM_ACTION => self::ACTION_COMPARE_CONTENT_OBJECTS
                );

                $version_browser = new VersionTable($this);

                $version_tab_content = array();
                $version_tab_content[] = $version_browser->as_html();
                $version_tab_content[] = ResourceManager::getInstance()->get_resource_html(
                    Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository', true) . 'Repository.js'
                );
                $this->tabs->add_tab(
                    new DynamicContentTab(
                        'versions',
                        Translation::get('Versions'),
                        Theme::getInstance()->getImagePath('Chamilo\Core\Repository', 'PlaceMini/Versions'),
                        implode(PHP_EOL, $version_tab_content)
                    )
                );
            }

            $this->add_links_to_content_object_tabs($object);
            $html = array();
            $html[] = $this->render_header();

            if ($this->getButtonToolbarRenderer($object))
            {
                $html[] = '<br />' . $this->buttonToolbarRenderer->render();
            }

            $html[] = $display->render();
            $html[] = $this->tabs->render();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get(
                        'NoObjectSelected',
                        array('OBJECT' => Translation::get('ContentObject')),
                        Utilities::COMMON_LIBRARIES
                    )
                )
            );
        }
    }

    private function getButtonToolbarRenderer($contentObject)
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $baseActions = new ButtonGroup();
            $publishActions = new ButtonGroup();
            $stateActions = new ButtonGroup();

            $rightsService = RightsService::getInstance();

            $contentObjectUnlinkAllowed =
                $this->getContentObjectPublicationManager()->canContentObjectBeUnlinked($contentObject);
            $contentObjectDeletionAllowed = DataManager::content_object_deletion_allowed($contentObject);

            $isRecycled = $contentObject->get_state() == ContentObject::STATE_RECYCLED;

            if ($contentObject->is_latest_version())
            {
                if ($rightsService->canDestroyContentObject($this->get_user(), $contentObject, $this->getWorkspace()))
                {
                    // Move to recycle bin
                    if ($contentObjectUnlinkAllowed && !$isRecycled)
                    {
                        $recycle_url = $this->get_content_object_recycling_url($contentObject);
                        $stateActions->addButton(
                            new Button(
                                Translation::get('Remove', null, Utilities::COMMON_LIBRARIES),
                                new FontAwesomeGlyph('trash'),
                                $recycle_url,
                                Button::DISPLAY_ICON_AND_LABEL
                            )
                        );
                    }

                    // Delete permanently
                    if ($contentObjectDeletionAllowed && $isRecycled)
                    {
                        $delete_url = $this->get_content_object_deletion_url($contentObject);
                        $stateActions->addButton(
                            new Button(
                                Translation::get('Delete', null, Utilities::COMMON_LIBRARIES),
                                new FontAwesomeGlyph('times'),
                                $delete_url,
                                Button::DISPLAY_ICON_AND_LABEL,
                                Translation::get('ConfirmDelete', null, Utilities::COMMON_LIBRARIES)
                            )
                        );
                    }

                    // Unlink
                    if (!$contentObjectDeletionAllowed && !$isRecycled && $contentObjectUnlinkAllowed)
                    {
                        $unlink_url = $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_UNLINK_CONTENT_OBJECTS,
                                self::PARAM_CONTENT_OBJECT_ID => $contentObject->get_id()
                            )
                        );

                        $stateActions->addButton(
                            new Button(
                                Translation::get('Unlink', null, Utilities::COMMON_LIBRARIES),
                                new FontAwesomeGlyph('link'),
                                $unlink_url,
                                Button::DISPLAY_ICON_AND_LABEL,
                                true
                            )
                        );
                    }

                    // Restore
                    if ($isRecycled)
                    {
                        $restore_url = $this->get_content_object_restoring_url($contentObject);
                        $stateActions->addButton(
                            new Button(
                                Translation::get('Restore', null, Utilities::COMMON_LIBRARIES),
                                new FontAwesomeGlyph('repeat'),
                                $restore_url,
                                Button::DISPLAY_ICON_AND_LABEL,
                                true
                            )
                        );
                    }
                }

                if ($rightsService->canEditContentObject($this->get_user(), $contentObject, $this->getWorkspace()))
                {
                    if (!$isRecycled)
                    {
                        if ($this->is_allowed_to_modify())
                        {
                            // Edit
                            $edit_url = $this->get_content_object_editing_url($contentObject);
                            $baseActions->addButton(
                                new Button(
                                    Translation::get('Edit', null, Utilities::COMMON_LIBRARIES),
                                    new FontAwesomeGlyph('pencil'),
                                    $edit_url,
                                    Button::DISPLAY_ICON_AND_LABEL
                                )
                            );
                        }

                        // Move
                        if (DataManager::workspace_has_categories($this->getWorkspace()))
                        {
                            $move_url = $this->get_content_object_moving_url($contentObject);
                            $baseActions->addButton(
                                new Button(
                                    Translation::get('Move', null, Utilities::COMMON_LIBRARIES),
                                    new FontAwesomeGlyph('folder-open'),
                                    $move_url,
                                    Button::DISPLAY_ICON_AND_LABEL
                                )
                            );
                        }

                        if (\Chamilo\Core\Repository\Builder\Manager::exists($contentObject->package()))
                        {
                            $baseActions->addButton(
                                new Button(
                                    Translation::get('BuildComplexObject', null, Utilities::COMMON_LIBRARIES),
                                    new FontAwesomeGlyph('cubes'),
                                    $this->get_browse_complex_content_object_url($contentObject),
                                    Button::DISPLAY_ICON_AND_LABEL
                                )
                            );

                            $preview_url = $this->get_preview_content_object_url($contentObject);
                            $onclick = '" onclick="javascript:openPopup(\'' . $preview_url . '\'); return false;';

                            $baseActions->addButton(
                                new Button(
                                    Translation::get('Preview', null, Utilities::COMMON_LIBRARIES),
                                    new FontAwesomeGlyph('desktop'),
                                    $preview_url,
                                    Button::DISPLAY_ICON_AND_LABEL,
                                    false,
                                    $onclick,
                                    '_blank'
                                )
                            );
                        }
                        else
                        {
                            if ($contentObject instanceof ComplexContentObjectSupport)
                            {
                                $image = new FontAwesomeGlyph('cubes');
                                $variable = 'BuildPreview';
                            }
                            else
                            {
                                $image = new FontAwesomeGlyph('desktop');
                                $variable = 'Preview';
                            }
                            $preview_url = $this->get_preview_content_object_url($contentObject);
                            $onclick = '" onclick="javascript:openPopup(\'' . $preview_url . '\'); return false;';

                            $baseActions->addButton(
                                new Button(
                                    Translation::get($variable, null, Utilities::COMMON_LIBRARIES),
                                    $image,
                                    $preview_url,
                                    Button::DISPLAY_ICON_AND_LABEL,
                                    false,
                                    $onclick,
                                    '_blank'
                                )
                            );
                        }
                    }
                }

                // Copy
                if ($rightsService->canCopyContentObject($this->get_user(), $contentObject, $this->getWorkspace()))
                {
                    $baseActions->addButton(
                        new Button(
                            Translation::get('Duplicate', null, Utilities::COMMON_LIBRARIES),
                            new FontAwesomeGlyph('files-o'),
                            $this->get_copy_content_object_url($contentObject->get_id())
                        )
                    );
                }

                // Publish
                if ($rightsService->canUseContentObject($this->get_user(), $contentObject, $this->getWorkspace()))
                {
                    $publishActions->addButton(
                        new Button(
                            Translation::get('Publish', null, Utilities::COMMON_LIBRARIES),
                            new FontAwesomeGlyph('share-quare-o'),
                            $this->get_publish_content_object_url($contentObject)
                        )
                    );
                }

                // Share
                if ($this->getWorkspace() instanceof PersonalWorkspace)
                {
                    $publishActions->addButton(
                        new Button(
                            Translation::get('Share', null, Utilities::COMMON_LIBRARIES),
                            new FontAwesomeGlyph('lock'),
                            $this->get_url(
                                array(
                                    Manager::PARAM_ACTION => Manager::ACTION_WORKSPACE,
                                    Manager::PARAM_CONTENT_OBJECT_ID => $contentObject->get_id(),
                                    \Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Manager::ACTION_SHARE
                                )
                            )
                        )
                    );
                }
                else
                {
                    if ($rightsService->canDeleteContentObject(
                        $this->get_user(), $contentObject, $this->getWorkspace()
                    ))
                    {
                        $url = $this->get_url(
                            array(
                                Manager::PARAM_ACTION => Manager::ACTION_WORKSPACE,
                                \Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Manager::ACTION_UNSHARE,
                                Manager::PARAM_CONTENT_OBJECT_ID => $contentObject->getId()
                            )
                        );

                        $stateActions->addButton(
                            new Button(
                                Translation::get('Unshare', null, Utilities::COMMON_LIBRARIES),
                                new FontAwesomeGlyph('unlock'),
                                $url,
                                Button::DISPLAY_ICON_AND_LABEL,
                                true
                            )
                        );
                    }
                }
            }
            else
            {
                // Revert
                if ($rightsService->canEditContentObject($this->get_user(), $contentObject, $this->getWorkspace()))
                {
                    $revert_url = $this->get_content_object_revert_url($contentObject, 'version');
                    $stateActions->addButton(
                        new Button(
                            Translation::get('Revert', null, Utilities::COMMON_LIBRARIES),
                            new FontAwesomeGlyph('repeat'),
                            $revert_url,
                            Button::DISPLAY_ICON_AND_LABEL
                        )
                    );
                }

                // Delete
                if ($this->canDestroyContentObject($contentObject))
                {
                    $deleteUrl = $this->get_content_object_deletion_url($contentObject, 'version');
                    $stateActions->addButton(
                        new Button(
                            Translation::get('Delete', null, Utilities::COMMON_LIBRARIES),
                            new FontAwesomeGlyph('times'),
                            $deleteUrl,
                            Button::DISPLAY_ICON_AND_LABEL
                        )
                    );
                }
            }

            $buttonToolbar->addItem($baseActions);
            $buttonToolbar->addItem($publishActions);
            $this->addExportButton($buttonToolbar);
            $buttonToolbar->addItem($stateActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function canDestroyContentObject(ContentObject $contentObject)
    {
        $rightsService = RightsService::getInstance();

        if (!$rightsService->canDestroyContentObject($this->get_user(), $contentObject, $this->getWorkspace()))
        {
            return false;
        }

        return $this->getContentObjectPublicationManager()->canContentObjectBeUnlinked($contentObject);
    }

    public function add_links_to_content_object_tabs($content_object)
    {
        $renderer_name = ClassnameUtilities::getInstance()->getClassnameFromObject($this, true);
        $tabs = new DynamicTabsRenderer($renderer_name);
        $parameters = array(
            self::PARAM_CONTEXT => self::context(),
            self::PARAM_CONTENT_OBJECT_ID => $this->object->get_id(),
            self::PARAM_ACTION => self::ACTION_VIEW_CONTENT_OBJECTS
        );

        // EXTERNAL INSTANCES
        if ($content_object->is_external())
        {
            $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB] = 'external_instances';
            $browser = new ExternalLinkTable($this);
            $this->tabs->add_tab(
                new DynamicContentTab(
                    'external_instances',
                    Translation::get('ExternalInstances'),
                    Theme::getInstance()->getImagePath('Chamilo\Core\Repository', 'PlaceMini/ExternalInstance'),
                    $browser->as_html()
                )
            );
        }

        // LINKS | PUBLICATIONS
        if ($content_object->has_publications())
        {
            $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB] = LinkTable::TYPE_PUBLICATIONS;
            $browser = new LinkTable($this, LinkTable::TYPE_PUBLICATIONS);
            $this->tabs->add_tab(
                new DynamicContentTab(
                    LinkTable::TYPE_PUBLICATIONS,
                    Translation::get('Publications'),
                    Theme::getInstance()->getImagePath('Chamilo\Core\Repository', 'PlaceMini/Publications'),
                    $browser->as_html()
                )
            );
        }

        if ($this->getWorkspace() instanceof PersonalWorkspace)
        {
            $contentObjectRelationService = new ContentObjectRelationService(new ContentObjectRelationRepository());
            $workspaceCount = $contentObjectRelationService->countWorkspacesForContentObject($this->get_object());

            if ($workspaceCount > 0)
            {
                $tabName = 'shared_in';

                $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB] = $tabName;

                $browser = new SharedInTable($this);

                $this->tabs->add_tab(
                    new DynamicContentTab(
                        $tabName,
                        Translation::get('SharedIn'),
                        Theme::getInstance()->getImagePath('Chamilo\Core\Repository', 'PlaceMini/Rights'),
                        $browser->as_html()
                    )
                );
            }
        }

        // LINKS | PARENTS
        if ($content_object->has_parents())
        {
            $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB] = LinkTable::TYPE_PARENTS;
            $browser = new LinkTable($this, LinkTable::TYPE_PARENTS);
            $this->tabs->add_tab(
                new DynamicContentTab(
                    LinkTable::TYPE_PARENTS,
                    Translation::get('UsedIn'),
                    Theme::getInstance()->getImagePath('Chamilo\Core\Repository', 'PlaceMini/Parents'),
                    $browser->as_html()
                )
            );
        }

        // LINKS | CHILDREN
        if ($content_object->has_children())
        {
            $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB] = LinkTable::TYPE_CHILDREN;
            $browser = new LinkTable($this, LinkTable::TYPE_CHILDREN);
            $this->tabs->add_tab(
                new DynamicContentTab(
                    LinkTable::TYPE_CHILDREN,
                    Translation::get('Uses'),
                    Theme::getInstance()->getImagePath('Chamilo\Core\Repository', 'PlaceMini/Children'),
                    $browser->as_html()
                )
            );
        }

        // LINKS | ATTACHED TO
        if ($content_object->has_attachers())
        {
            $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB] = LinkTable::TYPE_ATTACHED_TO;
            $browser = new LinkTable($this, LinkTable::TYPE_ATTACHED_TO);
            $this->tabs->add_tab(
                new DynamicContentTab(
                    LinkTable::TYPE_ATTACHED_TO,
                    Translation::get('AttachedTo'),
                    Theme::getInstance()->getImagePath('Chamilo\Core\Repository', 'PlaceMini/AttachedTo'),
                    $browser->as_html()
                )
            );
        }

        // LINKS | ATTACHES
        if ($content_object->has_attachments())
        {
            $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB] = LinkTable::TYPE_ATTACHES;
            $browser = new LinkTable($this, LinkTable::TYPE_ATTACHES);
            $this->tabs->add_tab(
                new DynamicContentTab(
                    LinkTable::TYPE_ATTACHES,
                    Translation::get('Attaches'),
                    Theme::getInstance()->getImagePath('Chamilo\Core\Repository', 'PlaceMini/Attaches'),
                    $browser->as_html()
                )
            );
        }

        // LINKS | INCLUDED IN
        if ($content_object->has_includers())
        {
            $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB] = LinkTable::TYPE_INCLUDED_IN;
            $browser = new LinkTable($this, LinkTable::TYPE_INCLUDED_IN);
            $this->tabs->add_tab(
                new DynamicContentTab(
                    LinkTable::TYPE_INCLUDED_IN,
                    Translation::get('IncludedIn'),
                    Theme::getInstance()->getImagePath('Chamilo\Core\Repository', 'PlaceMini/IncludedIn'),
                    $browser->as_html()
                )
            );
        }

        // LINKS | INCLUDES
        if ($content_object->has_includes())
        {
            $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB] = LinkTable::TYPE_INCLUDES;
            $browser = new LinkTable($this, LinkTable::TYPE_INCLUDES);
            $this->tabs->add_tab(
                new DynamicContentTab(
                    LinkTable::TYPE_INCLUDES,
                    Translation::get('Includes'),
                    Theme::getInstance()->getImagePath('Chamilo\Core\Repository', 'PlaceMini/Includes'),
                    $browser->as_html()
                )
            );
        }
    }

    public function get_object()
    {
        return $this->object;
    }

    public function addExportButton(ButtonToolBar $buttonToolBar)
    {
        $types = ContentObjectExportImplementation::get_types_for_object($this->object->package());

        if (count($types) >= 0)
        {
            if (count($types) > 1)
            {
                $dropdownButton = new DropdownButton(Translation::get('Export'), new FontAwesomeGlyph('upload'));

                foreach ($types as $type)
                {
                    $dropdownButton->addSubButton(
                        new SubButton(
                            $this->getExportTypeLabel($type),
                            null,
                            $this->get_content_object_exporting_url($this->object, $type)
                        )
                    );
                }

                $buttonToolBar->addItem($dropdownButton);
            }
            else
            {
                $exportType = array_pop($types);
                $buttonToolBar->addItem(
                    new Button(
                        $this->getExportTypeLabel($exportType),
                        new FontAwesomeGlyph('upload'),
                        $this->get_content_object_exporting_url($this->object, $exportType)
                    )
                );
            }
        }
    }

    /**
     *
     * @param string $type
     *
     * @return string
     */
    private function getExportTypeLabel($type)
    {
        $translationVariable = 'ExportType' .
            StringUtilities::getInstance()->createString($type)->upperCamelize()->__toString();
        $translation = Translation::get($translationVariable, null, $this->object->package());

        if ($translation == $translationVariable)
        {
            $imagePath = Theme::getInstance()->getImagePath('Chamilo\Core\Repository', 'Export/' . $type);
            $translation = Translation::get($translationVariable);
        }

        return $translation;
    }

    public function get_table_condition($table_class_name)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_OBJECT_NUMBER),
            new StaticConditionVariable($this->object->get_object_number())
        );
    }

    /**
     * Returns whether or not a user can change the links * @return s bool
     */
    public function is_allowed_to_modify()
    {
        return $this->allowed_to_modify;
    }
}
