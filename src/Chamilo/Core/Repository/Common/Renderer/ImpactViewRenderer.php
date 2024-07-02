<?php
namespace Chamilo\Core\Repository\Common\Renderer;

use Chamilo\Configuration\Category\Form\ImpactViewForm;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Table\ImpactViewTableRenderer;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\DataClassParameters;

/**
 * Renderer to render the impact viewer
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImpactViewRenderer
{
    use DependencyInjectionContainerTrait;

    /**
     * @var string[]
     */
    private array $co_ids;

    private ImpactViewForm $form;

    private bool $has_impact;

    private Manager $parent;

    /**
     * @throws \Exception
     */
    public function __construct(Manager $parent, array $co_ids, bool $has_impact)
    {
        $this->parent = $parent;
        $this->co_ids = $co_ids;
        $this->has_impact = $has_impact;

        $this->form = new ImpactViewForm(
            $this->parent->get_url(
                [Manager::PARAM_CONTENT_OBJECT_ID => $co_ids]
            )
        );
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function render(Condition $co_condition): string
    {
        if ($this->has_impact)
        {
            $view = $this->renderImpactView($co_condition);
        }
        else
        {
            $view = '<div class="normal-message">' .
                $this->getTranslator()->trans('NoImpact', [], 'Chamilo\Core\Repository') . '</div>';
        }

        $html = [];

        $html[] = $this->parent->render_header();
        $html[] = $view;
        $html[] = $this->form->render();
        $html[] = $this->parent->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getImpactViewTableRenderer(): ImpactViewTableRenderer
    {
        return $this->getService(ImpactViewTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    public function get_parameters(): array
    {
        return $this->parent->get_parameters();
    }

    public function get_url($parameters = [], $filter = []): string
    {
        return $this->parent->get_url($parameters, $filter);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    private function renderImpactView(Condition $condition): string
    {
        $totalNumberOfItems = DataManager::count_active_content_objects(
            ContentObject::class, new DataClassParameters(condition: $condition)
        );

        $impactViewTableRenderer = $this->getImpactViewTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $impactViewTableRenderer->getParameterNames(), $impactViewTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $contentObjects = DataManager::retrieve_active_content_objects(
            ContentObject::class, new DataClassParameters(
                condition: $condition, orderBy: $impactViewTableRenderer->determineOrderBy(
                $tableParameterValues
            ), count: $tableParameterValues->getNumberOfItemsPerPage(), offset: $tableParameterValues->getOffset()
            )
        );

        return $impactViewTableRenderer->render($tableParameterValues, $contentObjects);
    }

    /**
     * @throws \QuickformException
     */
    public function validated(): bool
    {
        return $this->form->validate();
    }
}

