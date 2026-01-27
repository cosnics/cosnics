<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Service;

use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ActionBar\Form\ButtonSearchForm;
use QuickformException;

/**
 * @package Chamilo\Libraries\Format\Structure\ActionBar\Renderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ButtonToolBarRenderer
{

    private ButtonToolBar $buttonToolBar;

    private ?ButtonSearchForm $searchForm;

    public function __construct(ButtonToolBar $buttonToolBar, ?ButtonSearchForm $buttonSearchForm = null)
    {
        $this->buttonToolBar = $buttonToolBar;
        $this->searchForm = $buttonSearchForm;
    }

    /**
     * @throws \QuickformException
     */
    public function render(): string
    {
        $html = [];

        $html[] = '<div class="' . implode(' ', $this->determineClasses()) . '">';

        foreach ($this->getButtonToolBar()->getItems() as $buttonGroup)
        {
            $rendererClassName =
                __NAMESPACE__ . '\\' . ClassnameUtilities::getInstance()->getClassnameFromObject($buttonGroup) .
                'Renderer';
            $renderer = new $rendererClassName($buttonGroup);
            $html[] = $renderer->render($buttonGroup);
        }

        if ($this->getButtonToolBar()->getSearchUrl())
        {
            $html[] = $this->getSearchForm()->render();
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string[]
     */
    protected function determineClasses(): array
    {
        return array_merge(['btn-toolbar', 'btn-action-toolbar'], $this->getButtonToolBar()->getClasses());
    }

    public function getButtonToolBar(): ButtonToolBar
    {
        return $this->buttonToolBar;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable[] $properties
     */
    public function getConditions(array $properties = []): ?AndCondition
    {
        if (!is_array($properties))
        {
            $properties = [$properties];
        }

        $query = $this->getSearchQuery();

        if ($query && count($properties))
        {
            $searchQueryConditionGenerator = new SearchQueryConditionGenerator();

            return $searchQueryConditionGenerator->getSearchConditions($query, $properties);
        }

        return null;
    }

    /**
     * @throws \QuickformException
     */
    public function getSearchForm(): ButtonSearchForm
    {
        if (!isset($this->searchForm))
        {
            $this->searchForm = new ButtonSearchForm($this->getButtonToolBar()->getSearchUrl());
        }

        return $this->searchForm;
    }

    public function getSearchQuery(): ?string
    {
        try
        {
            return $this->getSearchForm()->getQuery();
        }
        catch (QuickformException)
        {
            return null;
        }
    }
}