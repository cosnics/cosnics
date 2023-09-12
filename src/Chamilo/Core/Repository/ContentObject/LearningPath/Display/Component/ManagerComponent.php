<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\TreeNodeTableRenderer;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Apply batch-actions on specific folders or items (move, delete, rights configuration)
 *
 * @package repository\content_object\portfolio\display
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ManagerComponent extends BaseHtmlTreeComponent
{

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function build()
    {
        $currentNode = $this->getCurrentTreeNode();

        if (!$this->canEditTreeNode($currentNode))
        {
            throw new NotAllowedException();
        }

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb($this->get_url(), $this->getTranslator()->trans('ManagerComponent', [], Manager::CONTEXT))
        );

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->renderTable();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    public function getTreeNodeTableRenderer(): TreeNodeTableRenderer
    {
        return $this->getService(TreeNodeTableRenderer::class);
    }

    /**
     * @return string
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = count($this->getCurrentTreeNode()->getChildNodes());

        $treeNodeTableRenderer = $this->getTreeNodeTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $treeNodeTableRenderer->getParameterNames(), $treeNodeTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $treeNodes = new ArrayCollection(
            array_slice(
                $this->getCurrentTreeNode()->getChildNodes(), $tableParameterValues->getOffset(),
                $tableParameterValues->getNumberOfItemsPerPage()
            )
        );

        return $treeNodeTableRenderer->legacyRender($this, $tableParameterValues, $treeNodes);
    }
}
