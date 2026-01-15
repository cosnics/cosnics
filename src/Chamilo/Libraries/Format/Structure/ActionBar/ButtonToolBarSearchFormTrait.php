<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use QuickformException;

/**
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonToolBarSearchFormTrait
{
    public function getButtonToolBarSearchCondition(?string $type = null): ?AndCondition
    {
        $searchProperties = $this->getButtonToolBarSearchProperties($type);

        return $this->getButtonToolbarRenderer()->getConditions($searchProperties);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[]
     */
    abstract public function getButtonToolBarSearchProperties(?string $type = null): array;

    abstract public function getButtonToolbarRenderer(): ButtonToolBarRenderer;

    abstract public function getRequest(): ChamiloRequest;

    public function setButtonToolBarSearchFormRequestQuery(): void
    {
        try
        {
            $searchForm = $this->getButtonToolbarRenderer()->getSearchForm();

            if (!$searchForm->clearFormSubmitted())
            {
                $this->getRequest()->query->set(ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY, $searchForm->getQuery());
            }
            else
            {
                $this->getRequest()->request->remove(ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY);
                $this->getRequest()->query->remove(ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY);
            }
        }
        catch (QuickformException)
        {
        }
    }

}