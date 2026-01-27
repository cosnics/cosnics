<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait;

use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\UserInterface\ActionBar\Form\ButtonSearchForm;
use Chamilo\Libraries\UserInterface\ActionBar\Service\ButtonToolBarRenderer;
use QuickformException;

/**
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonToolBarSearchFormTrait
{
    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getButtonToolBarSearchCondition(?string $type = null): ?AndCondition
    {
        $searchProperties = $this->getButtonToolBarSearchProperties($type);

        return $this->getButtonToolbarRenderer()->getConditions($searchProperties);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable[]
     */
    abstract public function getButtonToolBarSearchProperties(?string $type = null): array;

    abstract public function getButtonToolbarRenderer(): ButtonToolBarRenderer;

    abstract public function getRequest(): ChamiloRequest;

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
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