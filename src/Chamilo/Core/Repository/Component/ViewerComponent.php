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
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
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
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use InvalidArgumentException;

/**
 *
 * @package repository.lib.repository_manager.component
 */

/**
 * Repository manager component which can be used to view a learning object.
 */
class ViewerComponent extends Manager implements DelegateComponent, TableSupport
{

    /**
     * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    private $contentObject;

    /**
     * @return string
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Exception
     */
    public function run()
    {
        $contentObject = $this->getContentObject();

        if (!$contentObject instanceof ContentObject)
        {
            throw new ObjectNotExistException($contentObject->getType());
        }

        if (!RightsService::getInstance()->canViewContentObject(
            $this->getUser(), $contentObject, $this->getWorkspace()
        ))
        {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();

        $display = ContentObjectRenditionImplementation::factory(
            $contentObject, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_FULL, $this
        );
        $trail = BreadcrumbTrail::getInstance();

        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb(
                null, $translator->trans(
                'ViewContentObject', array('{CONTENT_OBJECT}' => $contentObject->get_title()), self::package()
            )
            )
        );

        if ($contentObject->get_state() == ContentObject::STATE_RECYCLED)
        {
            $trail->add(
                new Breadcrumb($this->get_recycle_bin_url(), $translator->trans('RecycleBin', array(), self::package()))
            );
            $this->force_menu_url($this->get_recycle_bin_url());
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->getButtonToolbarRenderer()->render();
        $html[] = $display->render();
        $html[] = $this->getDynamicTabsRenderer()->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer $dynamicTabsRenderer
     *
     * @throws \Exception
     */
    public function addDynamicTabsRendererLinks(DynamicTabsRenderer $dynamicTabsRenderer)
    {
        $contentObject = $this->getContentObject();
        $translator = $this->getTranslator();

        $parameters = array(
            self::PARAM_CONTEXT => self::context(), self::PARAM_CONTENT_OBJECT_ID => $contentObject->getId(),
            self::PARAM_ACTION => self::ACTION_VIEW_CONTENT_OBJECTS
        );

        // EXTERNAL INSTANCES
        if ($contentObject->is_external())
        {
            $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB] = 'external_instances';
            $browser = new ExternalLinkTable($this);
            $dynamicTabsRenderer->add_tab(
                new DynamicContentTab(
                    'external_instances', $translator->trans('ExternalInstances', array(), self::package()),
                    new FontAwesomeGlyph('globe', array('fa-lg'), null, 'fas'), $browser->render()
                )
            );
        }

        // LINKS | PUBLICATIONS
        if ($contentObject->has_publications())
        {
            $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB] = LinkTable::TYPE_PUBLICATIONS;
            $browser = new LinkTable($this, LinkTable::TYPE_PUBLICATIONS);
            $dynamicTabsRenderer->add_tab(
                new DynamicContentTab(
                    LinkTable::TYPE_PUBLICATIONS, $translator->trans('Publications', array(), self::package()),
                    new FontAwesomeGlyph('share-square', array('fa-lg'), null, 'fas'), $browser->render()
                )
            );
        }

        if ($this->getWorkspace() instanceof PersonalWorkspace)
        {
            $contentObjectRelationService = new ContentObjectRelationService(new ContentObjectRelationRepository());
            $workspaceCount = $contentObjectRelationService->countWorkspacesForContentObject($contentObject);

            if ($workspaceCount > 0)
            {
                $tabName = 'shared_in';

                $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB] = $tabName;

                $browser = new SharedInTable($this);

                $dynamicTabsRenderer->add_tab(
                    new DynamicContentTab(
                        $tabName, $translator->trans('SharedIn', array(), self::package()),
                        new FontAwesomeGlyph('lock', array('fa-lg'), null, 'fas'), $browser->render()
                    )
                );
            }
        }

        // LINKS | PARENTS
        if ($contentObject->has_parents())
        {
            $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB] = LinkTable::TYPE_PARENTS;
            $browser = new LinkTable($this, LinkTable::TYPE_PARENTS);
            $dynamicTabsRenderer->add_tab(
                new DynamicContentTab(
                    LinkTable::TYPE_PARENTS, $translator->trans('UsedIn', array(), self::package()),
                    new FontAwesomeGlyph('arrow-up', array('fa-lg'), null, 'fas'), $browser->render()
                )
            );
        }

        // LINKS | CHILDREN
        if ($contentObject->has_children())
        {
            $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB] = LinkTable::TYPE_CHILDREN;
            $browser = new LinkTable($this, LinkTable::TYPE_CHILDREN);
            $dynamicTabsRenderer->add_tab(
                new DynamicContentTab(
                    LinkTable::TYPE_CHILDREN, $translator->trans('Uses', array(), self::package()),
                    new FontAwesomeGlyph('arrow-down', array('fa-lg'), null, 'fas'), $browser->render()
                )
            );
        }

        // LINKS | ATTACHED TO
        if ($contentObject->has_attachers())
        {
            $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB] = LinkTable::TYPE_ATTACHED_TO;
            $browser = new LinkTable($this, LinkTable::TYPE_ATTACHED_TO);
            $dynamicTabsRenderer->add_tab(
                new DynamicContentTab(
                    LinkTable::TYPE_ATTACHED_TO, $translator->trans('AttachedTo', array(), self::package()),
                    new FontAwesomeGlyph('bookmark', array('fa-lg'), null, 'fas'), $browser->render()
                )
            );
        }

        // LINKS | ATTACHES
        if ($contentObject->has_attachments())
        {
            $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB] = LinkTable::TYPE_ATTACHES;
            $browser = new LinkTable($this, LinkTable::TYPE_ATTACHES);
            $dynamicTabsRenderer->add_tab(
                new DynamicContentTab(
                    LinkTable::TYPE_ATTACHES, $translator->trans('Attaches', array(), self::package()),
                    new FontAwesomeGlyph('paperclip', array('fa-lg'), null, 'fas'), $browser->render()
                )
            );
        }

        // LINKS | INCLUDED IN
        if ($contentObject->has_includers())
        {
            $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB] = LinkTable::TYPE_INCLUDED_IN;
            $browser = new LinkTable($this, LinkTable::TYPE_INCLUDED_IN);
            $dynamicTabsRenderer->add_tab(
                new DynamicContentTab(
                    LinkTable::TYPE_INCLUDED_IN, $translator->trans('IncludedIn', array(), self::package()),
                    new FontAwesomeGlyph('expand-arrows-alt', array('fa-lg'), null, 'fas'), $browser->render()
                )
            );
        }

        // LINKS | INCLUDES
        if ($contentObject->has_includes())
        {
            $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB] = LinkTable::TYPE_INCLUDES;
            $browser = new LinkTable($this, LinkTable::TYPE_INCLUDES);
            $dynamicTabsRenderer->add_tab(
                new DynamicContentTab(
                    LinkTable::TYPE_INCLUDES, $translator->trans('Includes', array(), self::package()),
                    new FontAwesomeGlyph('compress-arrows-alt', array('fa-lg'), null, 'fas'), $browser->render()
                )
            );
        }
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return boolean
     */
    public function canDestroyContentObject(ContentObject $contentObject)
    {
        $rightsService = RightsService::getInstance();

        if (!$rightsService->canDestroyContentObject($this->getUser(), $contentObject, $this->getWorkspace()))
        {
            return false;
        }

        return $this->getPublicationAggregator()->canContentObjectBeUnlinked($contentObject);
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer
     */
    private function getButtonToolbarRenderer()
    {
        $contentObject = $this->getContentObject();

        $buttonToolbar = new ButtonToolBar();
        $baseActions = new ButtonGroup();
        $publishActions = new ButtonGroup();
        $stateActions = new ButtonGroup();

        $rightsService = RightsService::getInstance();
        $translator = $this->getTranslator();

        $contentObjectUnlinkAllowed = $this->getPublicationAggregator()->canContentObjectBeUnlinked($contentObject);
        $contentObjectDeletionAllowed = DataManager::content_object_deletion_allowed($contentObject);

        $isRecycled = $contentObject->get_state() == ContentObject::STATE_RECYCLED;

        if ($contentObject->is_current())
        {
            if ($rightsService->canDestroyContentObject($this->getUser(), $contentObject, $this->getWorkspace()))
            {
                // Move to recycle bin
                if ($contentObjectUnlinkAllowed && !$isRecycled)
                {
                    $recycle_url = $this->get_content_object_recycling_url($contentObject);
                    $stateActions->addButton(
                        new Button(
                            $translator->trans('Remove', array(), Utilities::COMMON_LIBRARIES),
                            new FontAwesomeGlyph('trash-alt'), $recycle_url, Button::DISPLAY_ICON_AND_LABEL
                        )
                    );
                }

                // Delete permanently
                if ($contentObjectDeletionAllowed && $isRecycled)
                {
                    $delete_url = $this->get_content_object_deletion_url($contentObject);
                    $stateActions->addButton(
                        new Button(
                            $translator->trans('Delete', array(), Utilities::COMMON_LIBRARIES),
                            new FontAwesomeGlyph('times'), $delete_url, Button::DISPLAY_ICON_AND_LABEL,
                            $translator->trans('ConfirmDelete', array(), Utilities::COMMON_LIBRARIES)
                        )
                    );
                }

                // Unlink
                if (!$contentObjectDeletionAllowed && !$isRecycled && $contentObjectUnlinkAllowed)
                {
                    $unlink_url = $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_UNLINK_CONTENT_OBJECTS,
                            self::PARAM_CONTENT_OBJECT_ID => $contentObject->getId()
                        )
                    );

                    $stateActions->addButton(
                        new Button(
                            $translator->trans('Unlink', array(), Utilities::COMMON_LIBRARIES),
                            new FontAwesomeGlyph('unlink', array(), null, 'fas'), $unlink_url,
                            Button::DISPLAY_ICON_AND_LABEL, true
                        )
                    );
                }

                // Restore
                if ($isRecycled)
                {
                    $restore_url = $this->get_content_object_restoring_url($contentObject);
                    $stateActions->addButton(
                        new Button(
                            $translator->trans('Restore', array(), Utilities::COMMON_LIBRARIES),
                            new FontAwesomeGlyph('undo'), $restore_url, Button::DISPLAY_ICON_AND_LABEL, true
                        )
                    );
                }
            }

            if ($rightsService->canEditContentObject($this->getUser(), $contentObject, $this->getWorkspace()))
            {
                if (!$isRecycled)
                {
                    if ($this->isAllowedToModify())
                    {
                        // Edit
                        $edit_url = $this->get_content_object_editing_url($contentObject);
                        $baseActions->addButton(
                            new Button(
                                $translator->trans('Edit', array(), Utilities::COMMON_LIBRARIES),
                                new FontAwesomeGlyph('pencil-alt'), $edit_url, Button::DISPLAY_ICON_AND_LABEL
                            )
                        );
                    }

                    // Move
                    if (DataManager::workspace_has_categories($this->getWorkspace()))
                    {
                        $move_url = $this->get_content_object_moving_url($contentObject);
                        $baseActions->addButton(
                            new Button(
                                $translator->trans('Move', array(), Utilities::COMMON_LIBRARIES),
                                new FontAwesomeGlyph('folder-open'), $move_url, Button::DISPLAY_ICON_AND_LABEL
                            )
                        );
                    }

                    if (\Chamilo\Core\Repository\Builder\Manager::exists($contentObject->package()))
                    {
                        $baseActions->addButton(
                            new Button(
                                $translator->trans('BuildComplexObject', array(), Utilities::COMMON_LIBRARIES),
                                new FontAwesomeGlyph('cubes'),
                                $this->get_browse_complex_content_object_url($contentObject),
                                Button::DISPLAY_ICON_AND_LABEL
                            )
                        );

                        $preview_url = $this->get_preview_content_object_url($contentObject);
                        $onclick = '" onclick="javascript:openPopup(\'' . $preview_url . '\'); return false;';

                        $baseActions->addButton(
                            new Button(
                                $translator->trans('Preview', array(), Utilities::COMMON_LIBRARIES),
                                new FontAwesomeGlyph('desktop'), $preview_url, Button::DISPLAY_ICON_AND_LABEL, false,
                                $onclick, '_blank'
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
                                $translator->trans($variable, array(), Utilities::COMMON_LIBRARIES), $image,
                                $preview_url, Button::DISPLAY_ICON_AND_LABEL, false, $onclick, '_blank'
                            )
                        );
                    }
                }
            }

            // Copy
            if ($rightsService->canCopyContentObject($this->getUser(), $contentObject, $this->getWorkspace()))
            {
                $baseActions->addButton(
                    new Button(
                        $translator->trans('Duplicate', array(), Utilities::COMMON_LIBRARIES),
                        new FontAwesomeGlyph('copy'), $this->get_copy_content_object_url($contentObject->getId())
                    )
                );
            }

            // Publish
            if ($rightsService->canUseContentObject($this->getUser(), $contentObject, $this->getWorkspace()))
            {
                $publishActions->addButton(
                    new Button(
                        $translator->trans('Publish', array(), Utilities::COMMON_LIBRARIES),
                        new FontAwesomeGlyph('share-square'), $this->get_publish_content_object_url($contentObject)
                    )
                );
            }

            // Share
            if ($this->getWorkspace() instanceof PersonalWorkspace)
            {
                $publishActions->addButton(
                    new Button(
                        $translator->trans('Share', array(), Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('lock'),
                        $this->get_url(
                            array(
                                Manager::PARAM_ACTION => Manager::ACTION_WORKSPACE,
                                Manager::PARAM_CONTENT_OBJECT_ID => $contentObject->getId(),
                                \Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Manager::ACTION_SHARE
                            )
                        )
                    )
                );
            }
            else
            {
                if ($rightsService->canDeleteContentObject($this->getUser(), $contentObject, $this->getWorkspace()))
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
                            $translator->trans('Unshare', array(), Utilities::COMMON_LIBRARIES),
                            new FontAwesomeGlyph('unlock'), $url, Button::DISPLAY_ICON_AND_LABEL, true
                        )
                    );
                }
            }
        }
        else
        {
            // Revert
            if ($rightsService->canEditContentObject($this->getUser(), $contentObject, $this->getWorkspace()))
            {
                $revert_url = $this->get_content_object_revert_url($contentObject);
                $stateActions->addButton(
                    new Button(
                        $translator->trans('Revert', array(), Utilities::COMMON_LIBRARIES),
                        new FontAwesomeGlyph('undo'), $revert_url, Button::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            // Delete
            if ($this->canDestroyContentObject($contentObject))
            {
                $deleteUrl = $this->get_content_object_deletion_url($contentObject, 'version');
                $stateActions->addButton(
                    new Button(
                        $translator->trans('Delete', array(), Utilities::COMMON_LIBRARIES),
                        new FontAwesomeGlyph('times'), $deleteUrl, Button::DISPLAY_ICON_AND_LABEL
                    )
                );
            }
        }

        $buttonToolbar->addItem($baseActions);
        $buttonToolbar->addItem($publishActions);
        $buttonToolbar->addItem($this->getExportButton());
        $buttonToolbar->addItem($stateActions);

        return new ButtonToolBarRenderer($buttonToolbar);
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function getContentObject()
    {
        if (!isset($this->contentObject))
        {
            $this->contentObject =
                DataManager::retrieve_by_id(ContentObject::class_name(), $this->getContentObjectIdentifier());
        }

        return $this->contentObject;
    }

    /**
     * @return integer
     */
    public function getContentObjectIdentifier()
    {
        $contentObjectIdentifier = $this->getRequest()->query->get(self::PARAM_CONTENT_OBJECT_ID);

        if (is_null($contentObjectIdentifier))
        {
            throw new InvalidArgumentException(
                $this->getTranslator()->trans(
                    'NoObjectSelected', array(), Utilities::COMMON_LIBRARIES
                )
            );
        }

        $this->set_parameter(self::PARAM_CONTENT_OBJECT_ID, $contentObjectIdentifier);

        return $contentObjectIdentifier;
    }

    /**
     * @return \Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer
     * @throws \Exception
     */
    protected function getDynamicTabsRenderer()
    {
        $dynamicTabsRenderer = new DynamicTabsRenderer('links');
        $contentObject = $this->getContentObject();

        if ($contentObject->get_current() != ContentObject::CURRENT_SINGLE)
        {
            $versionTable = new VersionTable($this);

            $versionTabContent = array();

            $versionTabContent[] = $versionTable->render();
            $versionTabContent[] = ResourceManager::getInstance()->getResourceHtml(
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository', true) . 'VersionTable.js'
            );

            $dynamicTabsRenderer->add_tab(
                new DynamicContentTab(
                    'versions', $this->getTranslator()->trans('Versions', array(), self::package()),
                    new FontAwesomeGlyph('undo', array('fa-lg'), null, 'fas'), implode(PHP_EOL, $versionTabContent)
                )
            );
        }

        $this->addDynamicTabsRendererLinks($dynamicTabsRenderer);

        return $dynamicTabsRenderer;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\Button|\Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton
     */
    public function getExportButton()
    {
        $contentObject = $this->getContentObject();
        $types = ContentObjectExportImplementation::get_types_for_object($contentObject->package());

        if (count($types) > 1)
        {
            $dropdownButton = new DropdownButton(
                $this->getTranslator()->trans('Export', array(), Utilities::COMMON_LIBRARIES),
                new FontAwesomeGlyph('download')
            );

            foreach ($types as $type)
            {
                $dropdownButton->addSubButton(
                    new SubButton(
                        $this->getExportTypeLabel($type), null,
                        $this->get_content_object_exporting_url($contentObject, $type)
                    )
                );
            }

            return $dropdownButton;
        }
        else
        {
            $exportType = array_pop($types);

            return new Button(
                $this->getExportTypeLabel($exportType), new FontAwesomeGlyph('download'),
                $this->get_content_object_exporting_url($contentObject, $exportType)
            );
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
        $translator = $this->getTranslator();

        $translationVariable =
            'ExportType' . StringUtilities::getInstance()->createString($type)->upperCamelize()->__toString();
        $translation = $translator->trans($translationVariable, array(), $this->getContentObject()->package());

        if ($translation == $translationVariable)
        {
            $translation = $translator->trans($translationVariable, array(), 'Chamilo\Core\Repository');
        }

        return $translation;
    }

    /**
     * @param string $table_class_name
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition|\Chamilo\Libraries\Storage\Query\Condition\EqualityCondition
     */
    public function get_table_condition($table_class_name)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_OBJECT_NUMBER),
            new StaticConditionVariable($this->getContentObject()->get_object_number())
        );
    }

    /**
     * @return boolean
     */
    public function isAllowedToModify()
    {
        $contentObject = $this->getContentObject();

        return RightsService::getInstance()->canEditContentObject(
                $this->getUser(), $contentObject, $this->getWorkspace()
            ) && $this->getPublicationAggregator()->canContentObjectBeEdited($contentObject->getId());
    }
}
