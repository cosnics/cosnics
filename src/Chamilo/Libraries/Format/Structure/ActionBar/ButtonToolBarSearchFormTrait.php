<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonToolBarSearchFormTrait
{
    /**
     * @throws \QuickformException
     */
    public function getButtonToolBarSearchCondition(): ?Condition
    {
        $searchProperties = $this->getButtonToolBarSearchProperties();

        return $this->getButtonToolbarRenderer()->getConditions($searchProperties);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[]
     */
    abstract public function getButtonToolBarSearchProperties(): array;

    abstract public function getButtonToolbarRenderer(): ButtonToolBarRenderer;

    abstract public function getRequest(): ChamiloRequest;

    /**
     * @throws \QuickformException
     */
    public function setButtonToolBarSearchFormRequestParameter()
    {
        $searchForm = $this->getButtonToolbarRenderer()->getSearchForm();

        if ($searchForm->validate())
        {
            $this->getRequest()->query->set(ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY, $searchForm->getQuery());
        }
    }

}