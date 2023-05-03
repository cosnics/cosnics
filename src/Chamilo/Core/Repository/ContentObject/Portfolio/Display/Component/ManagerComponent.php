<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component;

use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Table\ItemTableRenderer;
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
class ManagerComponent extends ItemComponent
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function build()
    {
        if (!$this->get_parent()->is_allowed_to_view_content_object($this->get_current_node()))
        {
            throw new NotAllowedException();
        }

        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb($this->get_url(), $this->getTranslator()->trans('ManagerComponent', [], Manager::CONTEXT))
        );

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->renderTable();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function getItemTableRenderer(): ItemTableRenderer
    {
        return $this->getService(ItemTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = count($this->get_current_node()->get_children());

        $itemTableRenderer = $this->getItemTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $itemTableRenderer->getParameterNames(), $itemTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $nodes = new ArrayCollection($this->get_current_node()->get_children());

        return $itemTableRenderer->legacyRender($this, $tableParameterValues, $nodes);
    }
}
