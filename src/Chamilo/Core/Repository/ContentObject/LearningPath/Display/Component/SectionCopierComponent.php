<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeCopier;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;

/**
 * Component to copy sections from other learning paths to the current learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SectionCopierComponent extends BaseHtmlTreeComponent
{
    const PARAM_SELECTED_CONTENT_OBJECT = 'selected_content_object';
    const PARAM_SELECTED_WORKSPACE = 'selected_workspace';
    const PARAM_SELECTED_LEARNING_PATH_NODES = 'learning_path_selected_nodes';
    const PARAM_COPY_INSTEAD_OF_REUSE = 'copy_instead_of_reuse';
    const PARAM_SELECTED_COPY_CATEGORY = 'selected_copy_category';
    const PARAM_NEW_COPY_CATEGORY = 'new_copy_category';

    /**
     * Builds this component and returns it's response
     *
     * @return string
     *
     * @throws NotAllowedException
     */
    function build()
    {
        if (!$this->canEditCurrentTreeNode())
        {
            throw new NotAllowedException();
        }

        $selectedContentObject = $this->getRequest()->getFromPost(self::PARAM_SELECTED_CONTENT_OBJECT);
        $selectedWorkspace = $this->getRequest()->getFromPost(self::PARAM_SELECTED_WORKSPACE);

        $selectedLearningPathNodes = json_decode(
            $this->getRequest()->getFromPost(self::PARAM_SELECTED_LEARNING_PATH_NODES)
        );

        $copyInsteadOfReuse = (bool) $this->getRequest()->getFromPost(self::PARAM_COPY_INSTEAD_OF_REUSE);
        $selectedCopyCategory = $this->getRequest()->getFromPost(self::PARAM_SELECTED_COPY_CATEGORY);
        $newCopyCategoryName = $this->getRequest()->getFromPost(self::PARAM_NEW_COPY_CATEGORY);

        $translator = Translation::getInstance();

        if (!empty($selectedContentObject) && !empty($selectedLearningPathNodes))
        {
            try
            {
                $contentObject = $this->getContentObjectRepository()->findById($selectedContentObject);
                if (!$contentObject instanceof LearningPath)
                {
                    throw new ObjectNotExistException(
                        $translator->getTranslation('LearningPath'),
                        $selectedContentObject
                    );
                }

                $workspaceService = new WorkspaceService(new WorkspaceRepository());
                $workspace = $workspaceService->determineWorkspaceForUserByIdentifier(
                    $this->getUser(),
                    (int) $selectedWorkspace
                );

                if (!$workspace instanceof WorkspaceInterface)
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
                    $this->getCurrentTreeNode(),
                    $contentObject,
                    $this->getUser(),
                    $selectedLearningPathNodes,
                    (bool) $copyInsteadOfReuse,
                    $selectedCopyCategory,
                    $newCopyCategoryName
                );

                $message = 'LearningPathNodesCopied';
                $success = true;
            }
            catch (\Exception $ex)
            {
                $this->getExceptionLogger()->logException($ex, ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR);
                $message = 'LearningPathNodesNotCopied';
                $success = false;
            }

            $this->redirect(
                Translation::getInstance()->getTranslation($message),
                !$success,
                array(self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT)
            );
        }

        return $this->renderCopyForm();
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

        $html = array();

        $html[] = $this->render_header();

        $javascriptFiles = array(
            'Repository/app.js',
            'Repository/service/RepositoryService.js',
            'LearningPathSectionCopier/app.js',
            'LearningPathSectionCopier/service/LearningPathService.js',
            'LearningPathSectionCopier/controller/MainController.js'
        );

        foreach ($javascriptFiles as $javascriptFile)
        {
            $html[] = ResourceManager::getInstance()->get_resource_html(
                $this->getPathBuilder()->getResourcesPath(Manager::context(), true) . 'Javascript/' . $javascriptFile
            );
        }

        $sectionCopierHtml = file_get_contents(
            $this->getPathBuilder()->getResourcesPath(Manager::context()) . 'Templates/SectionCopier.html'
        );

        $parameters = array('FORM_URL' => $this->get_url());

        foreach ($parameters as $parameter => $value)
        {
            $sectionCopierHtml = str_replace('{{ ' . $parameter . ' }}', $value, $sectionCopierHtml);
        }

        $html[] = $sectionCopierHtml;
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return ContentObjectRepository | object
     */
    protected function getContentObjectRepository()
    {
        return $this->getService('chamilo.core.repository.workspace.repository.content_object_repository');
    }

    /**
     *
     * @return RightsService
     */
    protected function getRightsService()
    {
        return RightsService::getInstance();
    }

    /**
     *
     * @return TreeNodeCopier | object
     */
    protected function getTreeNodeCopier()
    {
        return $this->getService(TreeNodeCopier::class);
    }
}
