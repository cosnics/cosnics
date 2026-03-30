<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait;

use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Form\ButtonSearchForm;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use QuickformException;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonToolBarSearchFormTrait
{
    protected readonly ButtonToolBarRenderer $buttonToolBarRenderer;

    public function getButtonToolBarSearchCondition(?string $type = null): ?AndCondition
    {
        $searchProperties = $this->getButtonToolBarSearchProperties($type);

        return $this->buttonToolBarRenderer->getConditions($searchProperties);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable[]
     */
    abstract public function getButtonToolBarSearchProperties(?string $type = null): array;

    abstract public function getRequest(): ChamiloRequest;

    public function setButtonToolBarSearchFormRequestQuery(): void
    {
        try {
            $searchForm = $this->buttonToolBarRenderer->getSearchForm();

            if (!$searchForm->clearFormSubmitted()) {
                $this->getRequest()->query->set(ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY, $searchForm->getQuery());
            }
            else {
                $this->getRequest()->request->remove(ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY);
                $this->getRequest()->query->remove(ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY);
            }
        }
        catch (QuickformException) {
        }
    }
}