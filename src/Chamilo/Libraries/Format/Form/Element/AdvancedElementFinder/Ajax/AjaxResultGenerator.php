<?php
namespace Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax;

use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;

/**
 * Helper class to build an ajax result for an advanced element finder ajax feed
 *
 * @package Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class AjaxResultGenerator
{
    public const PROPERTY_ELEMENTS = 'elements';
    public const PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    protected AjaxResultDataProviderInterface $ajaxResultDataProvider;

    protected int $offset;

    protected ?string $searchQuery;

    public function __construct(
        AjaxResultDataProviderInterface $ajaxResultDataProvider, ?string $searchQuery = null, int $offset = 0
    )
    {
        $this->setSearchQuery($searchQuery)->setOffset($offset)->setAjaxResultDataProvider($ajaxResultDataProvider);
    }

    /**
     * @return \Chamilo\Libraries\Architecture\JsonAjaxResult
     */
    public function generateAjaxResult(): JsonAjaxResult
    {
        $result = new JsonAjaxResult();

        $elements = new AdvancedElementFinderElements();
        $this->getAjaxResultDataProvider()->generateElements($elements);
        $elements = $elements->as_array();

        $result->set_property(self::PROPERTY_ELEMENTS, $elements);
        $result->set_property(
            self::PROPERTY_TOTAL_ELEMENTS, $this->getAjaxResultDataProvider()->getTotalNumberOfElements()
        );

        return $result;
    }

    /**
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax\AjaxResultDataProviderInterface
     */
    public function getAjaxResultDataProvider(): AjaxResultDataProviderInterface
    {
        return $this->ajaxResultDataProvider;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[] $searchProperties
     */
    public function getSearchCondition(array $searchProperties = []): ?AndCondition
    {
        $condition = null;

        if (!empty($this->searchQuery))
        {
            $searchQueryConditionGenerator = new SearchQueryConditionGenerator();
            $condition = $searchQueryConditionGenerator->getSearchConditions($this->searchQuery, $searchProperties);
        }

        return $condition;
    }

    public function getSearchQuery(): ?string
    {
        return $this->searchQuery;
    }

    public function setAjaxResultDataProvider(AjaxResultDataProviderInterface $ajaxResultDataProvider
    ): AjaxResultGenerator
    {
        $this->ajaxResultDataProvider = $ajaxResultDataProvider;

        return $this;
    }

    public function setOffset(int $offset = 0): AjaxResultGenerator
    {
        if (empty($offset))
        {
            $offset = 0;
        }

        $this->offset = $offset;

        return $this;
    }

    public function setSearchQuery(?string $searchQuery): AjaxResultGenerator
    {
        $this->searchQuery = $searchQuery;

        return $this;
    }
}