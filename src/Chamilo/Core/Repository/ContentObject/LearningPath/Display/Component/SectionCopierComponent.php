<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeCopier;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 * Component to copy sections from other learning paths to the current learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SectionCopierComponent extends BaseHtmlTreeComponent
{
    public const PARAM_COPY_INSTEAD_OF_REUSE = 'copy_instead_of_reuse';
    public const PARAM_SELECTED_CONTENT_OBJECT = 'selected_content_object';
    public const PARAM_SELECTED_LEARNING_PATH_NODES = 'learning_path_selected_nodes';
    public const PARAM_SELECTED_WORKSPACE = 'selected_workspace';

    /**
     * Builds this component and returns it's response
     *
     * @return string
     * @throws NotAllowedException
     */
    public function build()
    {
        if (!$this->canEditCurrentTreeNode())
        {
            throw new NotAllowedException();
        }

        $selectedContentObject = $this->getRequest()->getFromRequest(self::PARAM_SELECTED_CONTENT_OBJECT);
        $selectedWorkspace = $this->getRequest()->getFromRequest(self::PARAM_SELECTED_WORKSPACE);

        $selectedLearningPathNodes = json_decode(
            $this->getRequest()->getFromRequest(self::PARAM_SELECTED_LEARNING_PATH_NODES)
        );

        $copyInsteadOfReuse = (bool) $this->getRequest()->getFromRequest(self::PARAM_COPY_INSTEAD_OF_REUSE);

        $translator = Translation::getInstance();

        if (!empty($selectedContentObject) && !empty($selectedLearningPathNodes))
        {
            try
            {
                $contentObject = $this->getContentObjectRepository()->findById($selectedContentObject);
                if (!$contentObject instanceof LearningPath)
                {
                    throw new ObjectNotExistException(
                        $translator->getTranslation('LearningPath'), $selectedContentObject
                    );
                }

                $workspace = $this->getWorkspaceService()->determineWorkspaceForUserByIdentifier(
                    $this->getUser(), $selectedWorkspace
                );

                if (!$workspace instanceof Workspace)
                {
                    throw new ObjectNotExistException(
                        $translator->getTranslation('Workspace', null, 'Chamilo\Core\Repository'),
                        $selectedContentObject
                    );
                }

                $canUse = $this->getRightsService()->canUseContentObject($this->getUser(), $contentObject, $workspace);
                $canCopy =
                    $this->getRightsService()->canCopyContentObject($this->getUser(), $contentObject, $workspace);
                if (!$canUse && !$canCopy)
                {
                    throw new NotAllowedException();
                }

                $copyInsteadOfReuse = ($canUse && $canCopy) ? $copyInsteadOfReuse : $canCopy;

                $this->getTreeNodeCopier()->copyNodesFromLearningPath(
                    $this->getCurrentTreeNode(), $contentObject, $this->getUser(), $selectedLearningPathNodes,
                    (bool) $copyInsteadOfReuse
                );

                $message = 'LearningPathNodesCopied';
                $success = true;
            }
            catch (Exception $ex)
            {
                $this->getExceptionLogger()->logException($ex, ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR);
                $message = 'LearningPathNodesNotCopied';
                $success = false;
            }

            $this->redirectWithMessage(
                Translation::getInstance()->getTranslation($message), !$success,
                [self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT]
            );
        }

        return $this->renderCopyForm();
    }

    /**
     * @return ContentObjectRepository | object
     */
    protected function getContentObjectRepository()
    {
        return $this->getService(ContentObjectRepository::class);
    }

    protected function getCurrentWorkspace(): Workspace
    {
        return $this->getService('Chamilo\Core\Repository\CurrentWorkspace');
    }

    protected function getRightsService(): RightsService
    {
        return $this->getService(RightsService::class);
    }

    /**
     * @return TreeNodeCopier | object
     */
    protected function getTreeNodeCopier()
    {
        return $this->getService(TreeNodeCopier::class);
    }

    public function getWorkspaceService(): WorkspaceService
    {
        return $this->getService(WorkspaceService::class);
    }

    /**
     * Renders the form to select a content object and the nodes from the selected content object
     *
     * @return string
     */
    protected function renderCopyForm(): string
    {
        $breadcrumbTrail = BreadcrumbTrail::getInstance();
        $breadcrumbTrail->add(
            new Breadcrumb($this->get_url(), Translation::getInstance()->getTranslation('SectionCopierComponent'))
        );

        $html = [];

        $html[] = $this->render_header();

        $javascriptFiles = [
            'Repository/app.js',
            'Repository/service/RepositoryService.js',
            'LearningPathSectionCopier/app.js',
            'LearningPathSectionCopier/service/LearningPathService.js',
            'LearningPathSectionCopier/controller/MainController.js'
        ];

        foreach ($javascriptFiles as $javascriptFile)
        {
            $html[] = ResourceManager::getInstance()->getResourceHtml(
                $this->getPathBuilder()->getResourcesPath(Manager::context(), true) . 'Javascript/' . $javascriptFile
            );
        }

        $sectionCopierHtml = file_get_contents(
            $this->getPathBuilder()->getResourcesPath(Manager::context()) . 'Templates/SectionCopier.html'
        );

        $parameters = ['FORM_URL' => $this->get_url()];

        foreach ($parameters as $parameter => $value)
        {
            $sectionCopierHtml = str_replace('{{ ' . $parameter . ' }}', $value, $sectionCopierHtml);
        }

        $html[] = $sectionCopierHtml;
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}