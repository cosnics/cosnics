<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar\Renderer;

use Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Utilities\Utilities;

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
     */
    public function __construct(ButtonToolBar $buttonToolBar)
    {
        $this->buttonToolBar = $buttonToolBar;
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
    public function render()
    {
        $html = array();

        $html[] = '<div class="btn-toolbar btn-action-toolbar">';

        foreach ($this->getButtonToolBar()->getButtonGroups() as $buttonGroup)
        {
            $rendererClassName = __NAMESPACE__ . '\\' .
                 ClassnameUtilities :: getInstance()->getClassnameFromObject($buttonGroup) . 'Renderer';
            $renderer = new $rendererClassName($buttonGroup);
            $html[] = $renderer->render($buttonGroup);
        }

        if ($this->getButtonToolBar()->getSearchUrl())
        {
            $searchForm = $this->getSearchForm();

            if ($searchForm->validate() && $searchForm->clearFormSubmitted())
            {
                $redirectResponse = new RedirectResponse($this->getButtonToolBar()->getSearchUrl());
                $redirectResponse->send();
            }

            $html[] = $searchForm->render();
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm
     */
    public function getSearchForm()
    {
        if (! isset($this->searchForm))
        {
            $this->searchForm = new ButtonSearchForm($this->getButtonToolBar()->getSearchUrl());
        }

        return $this->searchForm;
    }

    /**
     * Returns the search query conditions
     *
     * @param array $properties
     * @return Condition
     * @uses Utilities :: query_to_condition() (deprecated)
     */
    public function getConditions($properties = array ())
    {
        // check input parameter
        if (! is_array($properties))
        {
            $properties = array($properties);
        }

        // get query
        $query = $this->getSearchForm()->getQuery();

        // only process if we have a search query and properties
        if (isset($query) && count($properties))
        {
            $search_conditions = Utilities :: query_to_condition($query, $properties);

            $condition = $search_conditions;
        }
        return $condition;
    }
}