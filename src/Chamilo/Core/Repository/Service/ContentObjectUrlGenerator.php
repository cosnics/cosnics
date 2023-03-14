<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Core\Repository\Architecture\DisplayAndBuildSupport;
use Chamilo\Core\Repository\Manager as RepositoryManager;
use Chamilo\Core\Repository\Preview\Manager as PreviewManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Manager as WorkspaceManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;

/**
 * @package Chamilo\Core\Repository\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectUrlGenerator
{
    protected UrlGenerator $urlGenerator;

    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param string[] $additionalParameters
     */
    protected function getActionUrl(
        string $context, string $actionParameterName, string $contentObjectParameterName, string $action,
        ContentObject $contentObject, array $additionalParameters = []
    ): string
    {
        $parameters = [
            Application::PARAM_CONTEXT => $context,
            $actionParameterName => $action,
            $contentObjectParameterName => $contentObject->getId()
        ];

        return $this->getUrlGenerator()->fromParameters(
            array_merge($parameters, $additionalParameters)
        );
    }

    public function getAlternativeUrl(ContentObject $contentObject): string
    {
        return $this->getRepositoryActionUrl(RepositoryManager::ACTION_LINK_CONTENT_OBJECT_ALTERNATIVE, $contentObject);
    }

    public function getBuildUrl(ContentObject $contentObject): string
    {
        return $this->getRepositoryActionUrl(RepositoryManager::ACTION_BUILD_COMPLEX_CONTENT_OBJECT, $contentObject);
    }

    public function getCopyUrl(ContentObject $contentObject): string
    {
        return $this->getRepositoryActionUrl(RepositoryManager::ACTION_COPY_CONTENT_OBJECT, $contentObject);
    }

    public function getDeleteUrl(ContentObject $contentObject, string $type = null): string
    {
        if (isset($type))
        {
            $parameterName = RepositoryManager::PARAM_DELETE_VERSION;
        }
        elseif ($contentObject->get_state() == ContentObject::STATE_RECYCLED)
        {
            $parameterName = RepositoryManager::PARAM_DELETE_PERMANENTLY;
        }
        else
        {
            $parameterName = RepositoryManager::PARAM_DELETE_RECYCLED;
        }

        return $this->getRepositoryActionUrl(
            RepositoryManager::ACTION_DELETE_CONTENT_OBJECTS, $contentObject, [$parameterName => 1]
        );
    }

    public function getDownloadUrl(ContentObject $contentObject): string
    {
        return $this->getRepositoryActionUrl(
            RepositoryManager::ACTION_DOWNLOAD_DOCUMENT, $contentObject,
            [ContentObject::PARAM_SECURITY_CODE => $contentObject->calculate_security_code()]
        );
    }

    public function getEditUrl(ContentObject $contentObject): string
    {
        return $this->getRepositoryActionUrl(RepositoryManager::ACTION_EDIT_CONTENT_OBJECTS, $contentObject);
    }

    public function getExportUrl(ContentObject $contentObject, ?string $type = null): string
    {
        return $this->getRepositoryActionUrl(
            RepositoryManager::ACTION_EXPORT_CONTENT_OBJECTS, $contentObject,
            [RepositoryManager::PARAM_EXPORT_TYPE => $type]
        );
    }

    public function getMoveUrl(ContentObject $contentObject): string
    {
        return $this->getRepositoryActionUrl(RepositoryManager::ACTION_MOVE_CONTENT_OBJECTS, $contentObject);
    }

    /**
     * @param string[] $additionalParameters
     */
    protected function getPreviewActionUrl(
        string $action, ContentObject $contentObject, array $additionalParameters = []
    ): string
    {
        return $this->getActionUrl(
            PreviewManager::CONTEXT, Application::PARAM_ACTION, PreviewManager::PARAM_CONTENT_OBJECT_ID, $action,
            $contentObject, $additionalParameters
        );
    }

    public function getPreviewDisplayUrl(ContentObject $contentObject): string
    {
        return $this->getPreviewActionUrl(PreviewManager::ACTION_DISPLAY, $contentObject);
    }

    public function getPreviewRenditionUrl(ContentObject $contentObject): string
    {
        return $this->getPreviewActionUrl(PreviewManager::ACTION_RENDITION, $contentObject);
    }

    public function getPreviewUrl(ContentObject $contentObject): string
    {
        if (is_subclass_of($contentObject, DisplayAndBuildSupport::class))
        {
            return $this->getPreviewDisplayUrl($contentObject);
        }
        else
        {
            return $this->getPreviewRenditionUrl($contentObject);
        }
    }

    public function getPublishUrl(ContentObject $contentObject): string
    {
        return $this->getRepositoryActionUrl(RepositoryManager::ACTION_PUBLICATION, $contentObject);
    }

    public function getRecycleUrl(ContentObject $contentObject): string
    {
        return $this->getRepositoryActionUrl(RepositoryManager::ACTION_IMPACT_VIEW_RECYCLE, $contentObject);
    }

    /**
     * @param string[] $additionalParameters
     */
    protected function getRepositoryActionUrl(
        string $action, ContentObject $contentObject, array $additionalParameters = []
    ): string
    {
        return $this->getActionUrl(
            RepositoryManager::CONTEXT, Application::PARAM_ACTION, RepositoryManager::PARAM_CONTENT_OBJECT_ID, $action,
            $contentObject, $additionalParameters
        );
    }

    public function getRestoreUrl(ContentObject $contentObject): string
    {
        return $this->getRepositoryActionUrl(RepositoryManager::ACTION_RESTORE_CONTENT_OBJECTS, $contentObject);
    }

    public function getRevertUrl(ContentObject $contentObject): string
    {
        return $this->getRepositoryActionUrl(RepositoryManager::ACTION_REVERT_CONTENT_OBJECTS, $contentObject);
    }

    public function getShareUrl(ContentObject $contentObject): string
    {
        return $this->getWorkspaceActionUrl(WorkspaceManager::ACTION_SHARE, $contentObject);
    }

    public function getUnshareUrl(ContentObject $contentObject): string
    {
        return $this->getWorkspaceActionUrl(WorkspaceManager::ACTION_UNSHARE, $contentObject);
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function getViewUrl(ContentObject $contentObject): string
    {
        return $this->getRepositoryActionUrl(RepositoryManager::ACTION_VIEW_CONTENT_OBJECTS, $contentObject);
    }

    /**
     * @param string[] $additionalParameters
     */
    protected function getWorkspaceActionUrl(
        string $action, ContentObject $contentObject, array $additionalParameters = []
    ): string
    {
        return $this->getActionUrl(
            WorkspaceManager::CONTEXT, WorkspaceManager::PARAM_ACTION, RepositoryManager::PARAM_CONTENT_OBJECT_ID,
            $action, $contentObject, $additionalParameters
        );
    }
}