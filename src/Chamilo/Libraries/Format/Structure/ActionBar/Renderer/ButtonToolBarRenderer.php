<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar\Renderer;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ButtonToolBarRenderer
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar
     */
    private $buttonToolBar;

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm
     */
    private $searchForm;

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar $buttonToolBar
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm $buttonSearchForm
     */
    public function __construct(ButtonToolBar $buttonToolBar, ButtonSearchForm $buttonSearchForm = null)
    {
        $this->buttonToolBar = $buttonToolBar;
        $this->searchForm = $buttonSearchForm;
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        $html = [];

        $html[] = '<div class="' . $this->getClasses() . '">';

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
                $redirectResponse = new RedirectResponse($searchForm->getActionURL());
                $redirectResponse->send();
            }

            $html[] = $searchForm->render();
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar
     */
    public function getButtonToolBar()
    {
        return $this->buttonToolBar;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar $buttonToolBar
     */
    public function setButtonToolBar(ButtonToolBar $buttonToolBar)
    {
        $this->buttonToolBar = $buttonToolBar;
    }

    /**
     *
     * @return string
     */
    protected function getClasses()
    {
        $classes = array('btn-toolbar', 'btn-action-toolbar');
        $classes = array_merge($classes, $this->getButtonToolBar()->getClasses());

        return implode(' ', $classes);
    }

    /**
     * Returns the search query conditions
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[] $properties
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     * @throws \Exception
     */
    public function getConditions($properties = [])
    {
        // check input parameter
        if (!is_array($properties))
        {
            $properties = array($properties);
        }

        // get query
        $query = $this->getSearchForm()->getQuery();

        // only process if we have a search query and properties
        if (isset($query) && count($properties))
        {
            $searchQueryConditionGenerator = new SearchQueryConditionGenerator();
            $search_conditions = $searchQueryConditionGenerator->getSearchConditions($query, $properties);

            $condition = $search_conditions;
        }

        return $condition;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm
     *
     * @throws \Exception
     */
    public function getSearchForm()
    {
        if (!isset($this->searchForm))
        {
            $this->searchForm = new ButtonSearchForm($this->getButtonToolBar()->getSearchUrl());
        }

        return $this->searchForm;
    }
}