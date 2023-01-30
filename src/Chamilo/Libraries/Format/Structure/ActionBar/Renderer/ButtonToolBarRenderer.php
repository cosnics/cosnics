<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar\Renderer;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
     * @throws \ReflectionException
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
            $searchForm = $this->getSearchForm();

            if ($searchForm->validate() && $searchForm->clearFormSubmitted())
            {
                $redirectResponse = new RedirectResponse($searchForm->getActionUrl());
                $redirectResponse->send();
            }

            $html[] = $searchForm->render();
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
     * @param \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[] $properties
     *
     * @throws \Exception
     */
    public function getConditions(array $properties = []): ?Condition
    {
        // check input parameter
        if (!is_array($properties))
        {
            $properties = [$properties];
        }

        // get query
        $query = $this->getSearchForm()->getQuery();

        // only process if we have a search query and properties
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

    public function setButtonToolBar(ButtonToolBar $buttonToolBar)
    {
        $this->buttonToolBar = $buttonToolBar;
    }
}