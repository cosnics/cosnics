<?php
namespace Chamilo\Libraries\UserInterface\Form\Service;

use Chamilo\Libraries\Protocol\Ajax\Architecture\Domain\JsonAjaxResult;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\UserInterface\Form\Architecture\Interface\AdvancedElementFinderAjaxResultDataProviderInterface;

/**
 * Helper class to build an ajax result for an advanced element finder ajax feed
 *
 * @package Chamilo\Libraries\UserInterface\Form\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class AdvancedElementFinderAjaxResultGenerator
{
    public const PROPERTY_ELEMENTS = 'elements';
    public const PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    protected AdvancedElementFinderAjaxResultDataProviderInterface $ajaxResultDataProvider;

    protected int $offset;

    protected ?string $searchQuery;

    public function __construct(
        AdvancedElementFinderAjaxResultDataProviderInterface $ajaxResultDataProvider, ?string $searchQuery = null,
        int $offset = 0
    )
    {
        $this->setSearchQuery($searchQuery)->setOffset($offset)->setAjaxResultDataProvider($ajaxResultDataProvider);
    }

    public function generateAjaxResult(): JsonAjaxResult
    {
        $result = new JsonAjaxResult();

        $elements = new AdvancedElementFinderElements();
        $this->getAjaxResultDataProvider()->generateElements($elements);
        $elements = $elements->asArray();

        $result->setProperty(self::PROPERTY_ELEMENTS, $elements);
        $result->setProperty(
            self::PROPERTY_TOTAL_ELEMENTS, $this->getAjaxResultDataProvider()->getTotalNumberOfElements()
        );

        return $result;
    }

    public function getAjaxResultDataProvider(): AdvancedElementFinderAjaxResultDataProviderInterface
    {
        return $this->ajaxResultDataProvider;
    }

    public function setAjaxResultDataProvider(
        AdvancedElementFinderAjaxResultDataProviderInterface $ajaxResultDataProvider
    ): AdvancedElementFinderAjaxResultGenerator
    {
        $this->ajaxResultDataProvider = $ajaxResultDataProvider;

        return $this;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset = 0): AdvancedElementFinderAjaxResultGenerator
    {
        if (empty($offset)) {
            $offset = 0;
        }

        $this->offset = $offset;

        return $this;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable[] $searchProperties
     */
    public function getSearchCondition(array $searchProperties = []): ?AndCondition
    {
        $condition = null;

        if (!empty($this->searchQuery)) {
            $searchQueryConditionGenerator = new SearchQueryConditionGenerator();
            $condition = $searchQueryConditionGenerator->getSearchConditions($this->searchQuery, $searchProperties);
        }

        return $condition;
    }

    public function getSearchQuery(): ?string
    {
        return $this->searchQuery;
    }

    public function setSearchQuery(?string $searchQuery): AdvancedElementFinderAjaxResultGenerator
    {
        $this->searchQuery = $searchQuery;

        return $this;
    }
}