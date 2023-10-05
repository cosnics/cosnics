<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Common\Export\ContentObjectExportImplementation;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Table\Link\LinkAttachedToTableRenderer;
use Chamilo\Core\Repository\Table\Link\LinkAttachesTableRenderer;
use Chamilo\Core\Repository\Table\Link\LinkChildrenTableRenderer;
use Chamilo\Core\Repository\Table\Link\LinkIncludeTableRenderer;
use Chamilo\Core\Repository\Table\Link\LinkParentsTableRenderer;
use Chamilo\Core\Repository\Table\Link\LinkPublicationsTableRenderer;
use Chamilo\Core\Repository\Table\Link\LinkTableRenderer;
use Chamilo\Core\Repository\Table\VersionTableRenderer;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Table\SharedInTableRenderer;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupportInterface;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Format\Tabs\ContentTab;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use InvalidArgumentException;

/**
 * @package repository.lib.repository_manager.component
 */

/**
 * Repository manager component which can be used to view a learning object.
 */
class ViewerComponent extends Manager implements BreadcrumbLessComponentInterface
{

    /**
     * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    private ContentObject $contentObject;

    /**
     * @return string
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

        if (!$this->getWorkspaceRightsService()->canViewContentObject(
            $this->getUser(), $contentObject, $this->getWorkspace()
        ))
        {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();

        $display = ContentObjectRenditionImplementation::factory(
            $contentObject, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_FULL
        );
        $trail = $this->getBreadcrumbTrail();

        $trail->add(
            new Breadcrumb(
                null, $translator->trans(
                'ViewContentObject', ['{CONTENT_OBJECT}' => $contentObject->get_title()], Manager::CONTEXT
            )
            )
        );

        if ($contentObject->get_state() == ContentObject::STATE_RECYCLED)
        {
            $trail->add(
                new Breadcrumb($this->get_recycle_bin_url(), $translator->trans('RecycleBin', [], Manager::CONTEXT))
            );
            $this->force_menu_url($this->get_recycle_bin_url());
        }

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->getButtonToolbarRenderer()->render();
        $html[] = $display->render();
        $html[] = $this->getTabsRenderer()->render('links', $this->getTabsCollection());
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Libraries\Format\Tabs\TabsCollection $tabs
     *
     * @throws \Exception
     */
    public function addDynamicTabsRendererLinks(TabsCollection $tabs)
    {
        $contentObject = $this->getContentObject();
        $translator = $this->getTranslator();

        // LINKS | PUBLICATIONS
        if ($contentObject->has_publications())
        {
            $tabs->add(
                new ContentTab(
                    (string) LinkTableRenderer::TYPE_PUBLICATIONS,
                    $translator->trans('Publications', [], Manager::CONTEXT), $this->renderLinkPublicationsTable(),
                    new FontAwesomeGlyph('share-square', ['fa-lg'], null, 'fas')
                )
            );
        }

        $totalNumberOfItems =
            $this->getContentObjectRelationService()->countWorkspaceAndRelationForContentObject($contentObject);

        if ($totalNumberOfItems > 0)
        {
            $tabName = 'shared_in';

            $sharedInTableRenderer = $this->getSharedInTableRenderer();

            $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
                $sharedInTableRenderer->getParameterNames(), $sharedInTableRenderer->getDefaultParameterValues(),
                $totalNumberOfItems
            );

            $sharedInWorkspaceRelations =
                $this->getContentObjectRelationService()->getWorkspaceAndRelationForContentObject(
                    $contentObject, $tableParameterValues->getOffset(),
                    $tableParameterValues->getNumberOfItemsPerPage(),
                    $sharedInTableRenderer->determineOrderBy($tableParameterValues)
                );

            $tabs->add(
                new ContentTab(
                    $tabName, $translator->trans('SharedIn', [], Manager::CONTEXT),
                    $sharedInTableRenderer->render($tableParameterValues, $sharedInWorkspaceRelations),
                    new FontAwesomeGlyph('lock', ['fa-lg'], null, 'fas')
                )
            );
        }

        // LINKS | PARENTS
        if ($contentObject->has_parents())
        {
            $tabs->add(
                new ContentTab(
                    (string) LinkTableRenderer::TYPE_PARENTS, $translator->trans('UsedIn', [], Manager::CONTEXT),
                    $this->renderLinkParentsTable(), new FontAwesomeGlyph('arrow-up', ['fa-lg'], null, 'fas')
                )
            );
        }

        // LINKS | CHILDREN
        if ($contentObject->has_children())
        {
            $tabs->add(
                new ContentTab(
                    (string) LinkTableRenderer::TYPE_CHILDREN, $translator->trans('Uses', [], Manager::CONTEXT),
                    $this->renderLinkChildrenTable(), new FontAwesomeGlyph('arrow-down', ['fa-lg'], null, 'fas')
                )
            );
        }

        // LINKS | ATTACHED TO
        if ($contentObject->has_attachers())
        {
            $tabs->add(
                new ContentTab(
                    (string) LinkTableRenderer::TYPE_ATTACHED_TO,
                    $translator->trans('AttachedTo', [], Manager::CONTEXT), $this->renderLinkAttachedtoTable(),
                    new FontAwesomeGlyph('bookmark', ['fa-lg'], null, 'fas')
                )
            );
        }

        // LINKS | ATTACHES
        if ($contentObject->has_attachments())
        {
            $tabs->add(
                new ContentTab(
                    (string) LinkTableRenderer::TYPE_ATTACHES, $translator->trans('Attaches', [], Manager::CONTEXT),
                    $this->renderLinkAttachesTable(), new FontAwesomeGlyph('paperclip', ['fa-lg'], null, 'fas')
                )
            );
        }

        // LINKS | INCLUDED IN
        if ($contentObject->has_includers())
        {
            $tabs->add(
                new ContentTab(
                    (string) LinkTableRenderer::TYPE_INCLUDED_IN,
                    $translator->trans('IncludedIn', [], Manager::CONTEXT), $this->renderLinkIncludedInTable(),
                    new FontAwesomeGlyph('expand-arrows-alt', ['fa-lg'], null, 'fas')
                )
            );
        }

        // LINKS | INCLUDES
        if ($contentObject->has_includes())
        {
            $tabs->add(
                new ContentTab(
                    (string) LinkTableRenderer::TYPE_INCLUDES, $translator->trans('Includes', [], Manager::CONTEXT),
                    $this->renderLinkIncludesTable(),
                    new FontAwesomeGlyph('compress-arrows-alt', ['fa-lg'], null, 'fas')
                )
            );
        }
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function canDestroyContentObject(ContentObject $contentObject): bool
    {
        if (!$this->getWorkspaceRightsService()->canDestroyContentObject(
            $this->getUser(), $contentObject, $this->getWorkspace()
        ))
        {
            return false;
        }

        return $this->getPublicationAggregator()->canContentObjectBeUnlinked($contentObject);
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer
     */
    private function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        $contentObject = $this->getContentObject();

        $buttonToolbar = new ButtonToolBar();
        $baseActions = new ButtonGroup();
        $publishActions = new ButtonGroup();
        $stateActions = new ButtonGroup();

        $rightsService = $this->getWorkspaceRightsService();
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
                            $translator->trans('Remove', [], StringUtilities::LIBRARIES),
                            new FontAwesomeGlyph('trash-alt'), $recycle_url, AbstractButton::DISPLAY_ICON_AND_LABEL
                        )
                    );
                }

                // Delete permanently
                if ($contentObjectDeletionAllowed && $isRecycled)
                {
                    $delete_url = $this->get_content_object_deletion_url($contentObject);
                    $stateActions->addButton(
                        new Button(
                            $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                            $delete_url, AbstractButton::DISPLAY_ICON_AND_LABEL,
                            $translator->trans('ConfirmDelete', [], StringUtilities::LIBRARIES)
                        )
                    );
                }

                // Unlink
                if (!$contentObjectDeletionAllowed && !$isRecycled && $contentObjectUnlinkAllowed)
                {
                    $unlink_url = $this->get_url(
                        [
                            self::PARAM_ACTION => self::ACTION_UNLINK_CONTENT_OBJECTS,
                            self::PARAM_CONTENT_OBJECT_ID => $contentObject->getId()
                        ]
                    );

                    $stateActions->addButton(
                        new Button(
                            $translator->trans('Unlink', [], StringUtilities::LIBRARIES),
                            new FontAwesomeGlyph('unlink', [], null, 'fas'), $unlink_url,
                            AbstractButton::DISPLAY_ICON_AND_LABEL,
                            $translator->trans('ConfirmChosenAction', [], StringUtilities::LIBRARIES)
                        )
                    );
                }

                // Restore
                if ($isRecycled)
                {
                    $restore_url = $this->get_content_object_restoring_url($contentObject);
                    $stateActions->addButton(
                        new Button(
                            $translator->trans('Restore', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('undo'),
                            $restore_url, AbstractButton::DISPLAY_ICON_AND_LABEL,
                            $translator->trans('ConfirmChosenAction', [], StringUtilities::LIBRARIES)
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
                                $translator->trans('Edit', [], StringUtilities::LIBRARIES),
                                new FontAwesomeGlyph('pencil-alt'), $edit_url, AbstractButton::DISPLAY_ICON_AND_LABEL
                            )
                        );
                    }

                    // Move
                    if (DataManager::workspace_has_categories($this->getWorkspace()))
                    {
                        $move_url = $this->get_content_object_moving_url($contentObject);
                        $baseActions->addButton(
                            new Button(
                                $translator->trans('Move', [], StringUtilities::LIBRARIES),
                                new FontAwesomeGlyph('folder-open'), $move_url, AbstractButton::DISPLAY_ICON_AND_LABEL
                            )
                        );
                    }

                    if ($contentObject::CONTEXT == 'Chamilo\Core\Repository\ContentObject\Assessment')
                    {
                        $baseActions->addButton(
                            new Button(
                                $translator->trans('BuildComplexObject', [], StringUtilities::LIBRARIES),
                                new FontAwesomeGlyph('cubes'),
                                $this->get_browse_complex_content_object_url($contentObject),
                                AbstractButton::DISPLAY_ICON_AND_LABEL
                            )
                        );

                        $preview_url = self::get_preview_content_object_url($contentObject);
                        $onclick =
                            '" onclick="javascript:openPopup(\'' . addslashes($preview_url) . '\'); return false;';

                        $baseActions->addButton(
                            new Button(
                                $translator->trans('Preview', [], StringUtilities::LIBRARIES),
                                new FontAwesomeGlyph('desktop'), $preview_url, AbstractButton::DISPLAY_ICON_AND_LABEL,
                                null, [$onclick], '_blank'
                            )
                        );
                    }
                    else
                    {
                        if ($contentObject instanceof ComplexContentObjectSupportInterface)
                        {
                            $image = new FontAwesomeGlyph('cubes');
                            $variable = 'BuildPreview';
                        }
                        else
                        {
                            $image = new FontAwesomeGlyph('desktop');
                            $variable = 'Preview';
                        }
                        $preview_url = $this::get_preview_content_object_url($contentObject);
                        $onclick =
                            '" onclick="javascript:openPopup(\'' . addslashes($preview_url) . '\'); return false;';

                        $baseActions->addButton(
                            new Button(
                                $translator->trans($variable, [], StringUtilities::LIBRARIES), $image, $preview_url,
                                AbstractButton::DISPLAY_ICON_AND_LABEL, null, [$onclick], '_blank'
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
                        $translator->trans('Duplicate', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('copy'),
                        $this->get_copy_content_object_url($contentObject->getId())
                    )
                );
            }

            // Publish
            if ($rightsService->canUseContentObject($this->getUser(), $contentObject, $this->getWorkspace()))
            {
                $publishActions->addButton(
                    new Button(
                        $translator->trans('Publish', [], StringUtilities::LIBRARIES),
                        new FontAwesomeGlyph('share-square'), $this->get_publish_content_object_url($contentObject)
                    )
                );
            }

            // Share
            $publishActions->addButton(
                new Button(
                    $translator->trans('Share', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('lock'),
                    $this->get_url(
                        [
                            Application::PARAM_ACTION => Manager::ACTION_WORKSPACE,
                            Manager::PARAM_CONTENT_OBJECT_ID => $contentObject->getId(),
                            \Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Manager::ACTION_SHARE
                        ]
                    )
                )
            );

            if ($rightsService->canDeleteContentObject($this->getUser(), $contentObject, $this->getWorkspace()))
            {
                $url = $this->get_url(
                    [
                        Application::PARAM_ACTION => Manager::ACTION_WORKSPACE,
                        \Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Manager::ACTION_UNSHARE,
                        Manager::PARAM_CONTENT_OBJECT_ID => $contentObject->getId()
                    ]
                );

                $stateActions->addButton(
                    new Button(
                        $translator->trans('Unshare', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('unlock'),
                        $url, AbstractButton::DISPLAY_ICON_AND_LABEL,
                        $this->getTranslator()->trans('ConfirmChosenAction', [], StringUtilities::LIBRARIES)
                    )
                );
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
                        $translator->trans('Revert', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('undo'),
                        $revert_url, AbstractButton::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            // Delete
            if ($this->canDestroyContentObject($contentObject))
            {
                $deleteUrl = $this->get_content_object_deletion_url($contentObject, 'version');
                $stateActions->addButton(
                    new Button(
                        $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                        $deleteUrl, AbstractButton::DISPLAY_ICON_AND_LABEL
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

    public function getContentObject(): ?ContentObject
    {
        if (!isset($this->contentObject))
        {
            $this->contentObject =
                DataManager::retrieve_by_id(ContentObject::class, (string) $this->getContentObjectIdentifier());
        }

        return $this->contentObject;
    }

    public function getContentObjectIdentifier(): int
    {
        $contentObjectIdentifier = $this->getRequest()->query->get(self::PARAM_CONTENT_OBJECT_ID);

        if (is_null($contentObjectIdentifier))
        {
            throw new InvalidArgumentException(
                $this->getTranslator()->trans(
                    'NoObjectSelected', [], StringUtilities::LIBRARIES
                )
            );
        }

        $this->set_parameter(self::PARAM_CONTENT_OBJECT_ID, $contentObjectIdentifier);

        return $contentObjectIdentifier;
    }

    protected function getContentObjectRelationService(): ContentObjectRelationService
    {
        return $this->getService(ContentObjectRelationService::class);
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\Button|\Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton
     */
    public function getExportButton()
    {
        $contentObject = $this->getContentObject();
        $types = ContentObjectExportImplementation::get_types_for_object($contentObject::CONTEXT);

        if (count($types) > 1)
        {
            $dropdownButton = new DropdownButton(
                $this->getTranslator()->trans('Export', [], StringUtilities::LIBRARIES),
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

    private function getExportTypeLabel(string $type): string
    {
        $translator = $this->getTranslator();

        $translationVariable =
            'ExportType' . StringUtilities::getInstance()->createString($type)->upperCamelize()->__toString();
        $translation = $translator->trans($translationVariable, [], $this->getContentObject()::CONTEXT);

        if ($translation == $translationVariable)
        {
            $translation = $translator->trans($translationVariable, [], 'Chamilo\Core\Repository');
        }

        return $translation;
    }

    protected function getLinkAttachedToTableRenderer(): LinkAttachedToTableRenderer
    {
        return $this->getService(LinkAttachedToTableRenderer::class);
    }

    protected function getLinkAttachesTableRenderer(): LinkAttachesTableRenderer
    {
        return $this->getService(LinkAttachesTableRenderer::class);
    }

    protected function getLinkChildrenTableRenderer(): LinkChildrenTableRenderer
    {
        return $this->getService(LinkChildrenTableRenderer::class);
    }

    protected function getLinkIncludeTableRenderer(): LinkIncludeTableRenderer
    {
        return $this->getService(LinkIncludeTableRenderer::class);
    }

    protected function getLinkParentsTableRenderer(): LinkParentsTableRenderer
    {
        return $this->getService(LinkParentsTableRenderer::class);
    }

    protected function getLinkPublicationsTableRenderer(): LinkPublicationsTableRenderer
    {
        return $this->getService(LinkPublicationsTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    public function getSharedInTableRenderer(): SharedInTableRenderer
    {
        return $this->getService(SharedInTableRenderer::class);
    }

    /**
     * @throws \Exception
     */
    protected function getTabsCollection(): TabsCollection
    {
        $tabs = new TabsCollection();
        $contentObject = $this->getContentObject();

        if ($contentObject->get_current() != ContentObject::CURRENT_SINGLE)
        {
            $totalNumberOfItems = DataManager::count_content_objects(
                ContentObject::class, new DataClassCountParameters($this->getVersionTableCondition())
            );

            $versionTableRenderer = $this->getVersionTableRenderer();

            $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
                $versionTableRenderer->getParameterNames(), $versionTableRenderer->getDefaultParameterValues(),
                $totalNumberOfItems
            );

            $contentObjects = DataManager::retrieve_content_objects(
                ContentObject::class, new DataClassRetrievesParameters(
                    $this->getVersionTableCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
                    $tableParameterValues->getOffset(), $versionTableRenderer->determineOrderBy($tableParameterValues)
                )
            );

            $versionTabContent = [];

            $versionTabContent[] = $versionTableRenderer->render($tableParameterValues, $contentObjects);
            $versionTabContent[] = ResourceManager::getInstance()->getResourceHtml(
                $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Core\Repository') . 'VersionTable.js'
            );

            $tabs->add(
                new ContentTab(
                    'versions', $this->getTranslator()->trans('Versions', [], Manager::CONTEXT),
                    implode(PHP_EOL, $versionTabContent), new FontAwesomeGlyph('undo', ['fa-lg'], null, 'fas')
                )
            );
        }

        $this->addDynamicTabsRendererLinks($tabs);

        return $tabs;
    }

    public function getVersionTableCondition(): EqualityCondition
    {
        return new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OBJECT_NUMBER),
            new StaticConditionVariable($this->getContentObject()->get_object_number())
        );
    }

    public function getVersionTableRenderer(): VersionTableRenderer
    {
        return $this->getService(VersionTableRenderer::class);
    }

    /**
     * @return bool
     */
    public function isAllowedToModify(): bool
    {
        $contentObject = $this->getContentObject();

        return $this->getWorkspaceRightsService()->canEditContentObject(
                $this->getUser(), $contentObject, $this->getWorkspace()
            ) && $this->getPublicationAggregator()->canContentObjectBeEdited((int) $contentObject->getId());
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderLinkAttachedtoTable(): string
    {
        $totalNumberOfItems = $this->getContentObject()->count_attachers();
        $attachedToTableRenderer = $this->getLinkAttachedToTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $attachedToTableRenderer->getParameterNames(), $attachedToTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $attachers = $this->getContentObject()->get_attachers(
            $attachedToTableRenderer->determineOrderBy($tableParameterValues), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage()
        );

        return $attachedToTableRenderer->render($tableParameterValues, $attachers);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderLinkAttachesTable(): string
    {
        $totalNumberOfItems = $this->getContentObject()->count_attachments();
        $attachesTableRenderer = $this->getLinkAttachesTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $attachesTableRenderer->getParameterNames(), $attachesTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $attachments = $this->getContentObject()->get_attachments(
            ContentObject::ATTACHMENT_NORMAL, $attachesTableRenderer->determineOrderBy($tableParameterValues),
            $tableParameterValues->getOffset(), $tableParameterValues->getNumberOfItemsPerPage()
        );

        return $attachesTableRenderer->render($tableParameterValues, $attachments);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderLinkChildrenTable(): string
    {
        $totalNumberOfItems = $this->getContentObject()->count_children();
        $childrenTableRenderer = $this->getLinkChildrenTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $childrenTableRenderer->getParameterNames(), $childrenTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $children = $this->getContentObject()->get_children(
            $childrenTableRenderer->determineOrderBy($tableParameterValues), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage()
        );

        return $childrenTableRenderer->render($tableParameterValues, $children);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderLinkIncludedInTable(): string
    {
        $totalNumberOfItems = $this->getContentObject()->count_includers();
        $includeTableRenderer = $this->getLinkIncludeTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $includeTableRenderer->getParameterNames(), $includeTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $includers = $this->getContentObject()->get_includers(
            $includeTableRenderer->determineOrderBy($tableParameterValues), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage()
        );

        return $includeTableRenderer->render($tableParameterValues, $includers);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderLinkIncludesTable(): string
    {
        $totalNumberOfItems = $this->getContentObject()->count_includes();
        $includeTableRenderer = $this->getLinkIncludeTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $includeTableRenderer->getParameterNames(), $includeTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $includes = $this->getContentObject()->get_includes(
            $includeTableRenderer->determineOrderBy($tableParameterValues), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage()
        );

        return $includeTableRenderer->render($tableParameterValues, $includes);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderLinkParentsTable(): string
    {
        $totalNumberOfItems = $this->getContentObject()->count_parents();
        $parentsTableRenderer = $this->getLinkParentsTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $parentsTableRenderer->getParameterNames(), $parentsTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $parents = $this->getContentObject()->get_parents(
            $parentsTableRenderer->determineOrderBy($tableParameterValues), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage()
        );

        return $parentsTableRenderer->render($tableParameterValues, $parents);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderLinkPublicationsTable(): string
    {
        $totalNumberOfItems = $this->getPublicationAggregator()->countPublicationAttributes(
            PublicationAggregatorInterface::ATTRIBUTES_TYPE_OBJECT, $this->getContentObjectIdentifier()
        );
        $publicationsTableRenderer = $this->getLinkPublicationsTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $publicationsTableRenderer->getParameterNames(), $publicationsTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $publications = $this->getPublicationAggregator()->getContentObjectPublicationsAttributes(
            PublicationAggregatorInterface::ATTRIBUTES_TYPE_OBJECT, $this->getContentObjectIdentifier(), null,
            $tableParameterValues->getNumberOfItemsPerPage(), $tableParameterValues->getOffset(),
            $publicationsTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $publicationsTableRenderer->render($tableParameterValues, $publications);
    }
}
