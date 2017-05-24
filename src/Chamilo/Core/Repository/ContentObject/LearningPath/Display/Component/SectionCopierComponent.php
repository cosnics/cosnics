<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Renderer\LearningPathTreeJSONMapper;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator\NodeActionGeneratorFactory;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;

/**
 * Component to copy sections from other learning paths to the current learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SectionCopierComponent extends BaseHtmlTreeComponent
{
    const PARAM_SELECTED_CONTENT_OBJECT = 'selected_content_object';
    const PARAM_SELECTED_LEARNING_PATH_NODES = 'learning_path_selected_nodes';
    const PARAM_COPY_INSTEAD_OF_REUSE = 'copy_instead_of_reuse';

    /**
     * Builds this component and returns it's response
     *
     * @return string
     *
     * @throws NotAllowedException
     */
    function build()
    {
        if(!$this->canEditLearningPathTreeNode($this->getCurrentLearningPathTreeNode()))
        {
            throw new NotAllowedException();
        }

        $selectedContentObject = $this->getRequest()->get(self::PARAM_SELECTED_CONTENT_OBJECT);
        $selectedLearningPathNodes = json_decode($this->getRequest()->get(self::PARAM_SELECTED_LEARNING_PATH_NODES));
        $copyInsteadOfReuse = $this->getRequest()->get(self::PARAM_COPY_INSTEAD_OF_REUSE);

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

                if(!$this->getRightsService()->canCopyContentObject($this->getUser(), $contentObject))
                {
                    throw new NotAllowedException();
                }

                $this->getLearningPathService()
                    ->copyNodesFromLearningPath(
                        $this->getCurrentLearningPathTreeNode(), $contentObject,
                        $selectedLearningPathNodes, (bool) $copyInsteadOfReuse
                    );

                $message = 'LearningPathNodesCopied';
                $success = true;
            }
            catch (\Exception $ex)
            {
                $message = 'LearningPathNodesNotCopied';
                $success = false;
            }

            $this->redirect(
                Translation::getInstance()->getTranslation($message), !$success,
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT))
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

        $sectionCopierHtml = file_get_contents(
            $this->getPathBuilder()->getResourcesPath(Manager::context()) . 'Templates/SectionCopier.html'
        );

        $parameters = array(
            'FORM_URL' => $this->get_url()
        );

        foreach ($parameters as $parameter => $value)
        {
            $sectionCopierHtml = str_replace('{{ ' . $parameter . ' }}', $value, $sectionCopierHtml);
        }

        $html[] = $sectionCopierHtml;
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return ContentObjectRepository | object
     */
    protected function getContentObjectRepository()
    {
        return $this->getService('chamilo.core.repository.workspace.repository.content_object_repository');
    }

    /**
     * @return RightsService
     */
    protected function getRightsService()
    {
        return RightsService::getInstance();
    }

    /**
     * @return LearningPathService | object
     */
    protected function getLearningPathService()
    {
        return $this->getService('chamilo.core.repository.content_object.learning_path.service.learning_path_service');
    }

}