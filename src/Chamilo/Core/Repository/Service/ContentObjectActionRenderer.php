<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Core\Repository\Architecture\BuildSupport;
use Chamilo\Core\Repository\Architecture\DownloadSupport;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectActionRenderer
{
    protected ContentObjectUrlGenerator $contentObjectUrlGenerator;

    protected RightsService $rightsService;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    protected User $user;

    protected Workspace $workspace;

    public function __construct(
        ContentObjectUrlGenerator $contentObjectUrlGenerator, RightsService $rightsService, Translator $translator,
        UrlGenerator $urlGenerator, User $user, Workspace $workspace
    )
    {
        $this->contentObjectUrlGenerator = $contentObjectUrlGenerator;
        $this->rightsService = $rightsService;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->user = $user;
        $this->workspace = $workspace;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    public function getActions(ContentObject $contentObject): array
    {
        $rightsService = $this->getRightsService();
        $translator = $this->getTranslator();

        $user = $this->getUser();
        $workspace = $this->getWorkspace();

        $actions = [];

        $canEditContentObject = $rightsService->canEditContentObject(
            $user, $contentObject, $workspace
        );

        $canDeleteContentObject = $rightsService->canDeleteContentObject(
            $user, $contentObject, $workspace
        );

        $canUseContentObject = $rightsService->canUseContentObject(
            $user, $contentObject, $workspace
        );

        $canCopyContentObject = $rightsService->canCopyContentObject(
            $user, $contentObject, $workspace
        );

        if ($canEditContentObject)
        {
            $actions[] = new ToolbarItem(
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $this->getContentObjectUrlGenerator()->getEditUrl($contentObject), ToolbarItem::DISPLAY_ICON
            );
        }

        if ($canCopyContentObject)
        {
            $actions[] = new ToolbarItem(
                $translator->trans('Duplicate'), new FontAwesomeGlyph('copy'),
                $this->getContentObjectUrlGenerator()->getCopyUrl($contentObject), ToolbarItem::DISPLAY_ICON
            );
        }

        if ($rightsService->isWorkspaceCreator($user, $workspace))
        {
            $actions[] = new ToolbarItem(
                $translator->trans('Remove', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('trash-alt'),
                $this->getContentObjectUrlGenerator()->getRecycleUrl($contentObject), ToolbarItem::DISPLAY_ICON, true
            );
        }

        if (DataManager::workspace_has_categories($workspace))
        {
            if ($canEditContentObject)
            {
                $actions[] = new ToolbarItem(
                    $translator->trans('Move', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('folder-open'),
                    $this->getContentObjectUrlGenerator()->getMoveUrl($contentObject), ToolbarItem::DISPLAY_ICON
                );
            }
        }

        if ($rightsService->isWorkspaceCreator($user, $workspace))
        {
            $actions[] = new ToolbarItem(
                $translator->trans('Share', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('lock'),
                $this->getContentObjectUrlGenerator()->getShareUrl($contentObject), ToolbarItem::DISPLAY_ICON
            );
        }
        elseif ($canDeleteContentObject)
        {
            $actions[] = new ToolbarItem(
                $translator->trans('Unshare', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('unlock'),
                $this->getContentObjectUrlGenerator()->getUnshareUrl($contentObject), ToolbarItem::DISPLAY_ICON, true
            );
        }

        if ($canCopyContentObject)
        {
            $actions[] = new ToolbarItem(
                $translator->trans('Export', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('download'),
                $this->getContentObjectUrlGenerator()->getExportUrl($contentObject), ToolbarItem::DISPLAY_ICON
            );
        }

        if ($canUseContentObject)
        {
            $actions[] = new ToolbarItem(
                $translator->trans('Publish', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('share-square'),
                $this->getContentObjectUrlGenerator()->getPublishUrl($contentObject), ToolbarItem::DISPLAY_ICON
            );
        }

        $preview_url = $this->getContentObjectUrlGenerator()->getPreviewUrl($contentObject);
        $onclick = '" onclick="javascript:openPopup(\'' . addslashes($preview_url) . '\'); return false;';

        if (is_subclass_of($contentObject, BuildSupport::class))
        {
            if ($canEditContentObject)
            {
                $actions[] = new ToolbarItem(
                    $translator->trans('BuildComplexObject', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('cubes'), $this->getContentObjectUrlGenerator()->getBuildUrl($contentObject),
                    ToolbarItem::DISPLAY_ICON
                );
            }

            $actions[] = new ToolbarItem(
                $translator->trans('Preview', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('desktop'),
                $preview_url, ToolbarItem::DISPLAY_ICON, false, $onclick, '_blank'
            );
        }
        elseif ($canEditContentObject)
        {
            $actions[] = new ToolbarItem(
                $translator->trans('BuildPreview', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('cubes'),
                $preview_url, ToolbarItem::DISPLAY_ICON, false, $onclick, '_blank'
            );
        }
        else
        {
            $actions[] = new ToolbarItem(
                $translator->trans('Preview', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('desktop'),
                $preview_url, ToolbarItem::DISPLAY_ICON, false, $onclick, '_blank'
            );
        }

        if (is_subclass_of($contentObject, DownloadSupport::class))
        {
            $actions[] = new ToolbarItem(
                $translator->trans('Download', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('download'),
                $this->getContentObjectUrlGenerator()->getDownloadUrl($contentObject), ToolbarItem::DISPLAY_ICON
            );
        }

        return $actions;
    }

    public function getContentObjectUrlGenerator(): ContentObjectUrlGenerator
    {
        return $this->contentObjectUrlGenerator;
    }

    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getWorkspace(): Workspace
    {
        return $this->workspace;
    }

    public function renderActions(ContentObject $contentObject): string
    {
        $toolbar = new Toolbar();

        $toolbar->add_items($this->getActions($contentObject));

        return $toolbar->render();
    }
}