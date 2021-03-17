<?php

namespace Chamilo\Libraries\Storage\FilterParameters;

use Chamilo\Libraries\Architecture\Exceptions\ValueNotInArrayException;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * Class FilterParametersBuilder
 * @package Chamilo\Libraries\Storage\FilterParameters
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class FilterParametersBuilder
{
    const PARAM_GLOBAL_SEARCH_QUERY = 'global_search_query';
    const PARAM_FIELDS_SEARCH_QUERY = 'fields_search_query'; // JSON array for all field search queries ['firstname' => 'John', 'lastname' => 'Doe']
    const PARAM_ITEMS_PER_PAGE = 'items_per_page';
    const PARAM_PAGE_NUMBER = 'page_number'; // Starts from 1
    const PARAM_SORT_FIELD = 'sort_field';
    const PARAM_SORT_DIRECTION = 'sort_direction';

    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';

    /**
     * @param ChamiloRequest $chamiloRequest
     * @param FieldMapper $fieldMapper
     *
     * @return FilterParameters
     */
    public function buildFilterParametersFromRequest(ChamiloRequest $chamiloRequest, FieldMapper $fieldMapper)
    {
        $filterParameters = new FilterParameters();

        $this->addGlobalSearchQueryToFilterParameters($filterParameters, $chamiloRequest)
            ->addFieldSearchQueriesToFilterParameters($filterParameters, $chamiloRequest, $fieldMapper)
            ->addItemsPerPageToFilterParameters($filterParameters, $chamiloRequest)
            ->addPageNumberToFilterParameters($filterParameters, $chamiloRequest)
            ->addSortToFilterParameters($filterParameters, $chamiloRequest, $fieldMapper);

        return $filterParameters;
    }

    /**
     * @param FilterParameters $filterParameters
     * @param ChamiloRequest $chamiloRequest
     *
     * @return FilterParametersBuilder
     */
    protected function addGlobalSearchQueryToFilterParameters(
        FilterParameters $filterParameters, ChamiloRequest $chamiloRequest
    )
    {
        $globalSearchQuery = $chamiloRequest->getFromPostOrUrl(self::PARAM_GLOBAL_SEARCH_QUERY);
        $filterParameters->setGlobalSearchQuery($globalSearchQuery);

        return $this;
    }

    /**
     * @param FilterParameters $filterParameters
     * @param ChamiloRequest $chamiloRequest
     * @param FieldMapper $fieldMapper
     *
     * @return FilterParametersBuilder
     */
    protected function addFieldSearchQueriesToFilterParameters(
        FilterParameters $filterParameters, ChamiloRequest $chamiloRequest, FieldMapper $fieldMapper
    )
    {
        $fieldsSearchQuery = $chamiloRequest->getFromPostOrUrl(self::PARAM_FIELDS_SEARCH_QUERY);
        $searchQueryPerField = json_decode($fieldsSearchQuery);
        if ($searchQueryPerField === false)
        {
            return $this;
        }

        foreach ($searchQueryPerField as $field => $searchQuery)
        {
            $filterParameters->addDataClassSearchQuery(
                new DataClassSearchQuery($fieldMapper->getConditionVariableForField($field), $searchQuery)
            );
        }

        return $this;
    }

    /**
     * @param FilterParameters $filterParameters
     * @param ChamiloRequest $chamiloRequest
     *
     * @return FilterParametersBuilder
     */
    protected function addItemsPerPageToFilterParameters(
        FilterParameters $filterParameters, ChamiloRequest $chamiloRequest
    )
    {
        $itemsPerPage = $chamiloRequest->getFromPostOrUrl(self::PARAM_ITEMS_PER_PAGE);
        $filterParameters->setCount($itemsPerPage);

        return $this;
    }

    /**
     * @param FilterParameters $filterParameters
     * @param ChamiloRequest $chamiloRequest
     *
     * @return FilterParametersBuilder
     */
    protected function addPageNumberToFilterParameters(
        FilterParameters $filterParameters, ChamiloRequest $chamiloRequest
    )
    {
        $pageNumber = $chamiloRequest->getFromPostOrUrl(self::PARAM_PAGE_NUMBER);
        $itemsPerPage = $chamiloRequest->getFromPostOrUrl(self::PARAM_ITEMS_PER_PAGE);

        if (empty($pageNumber) || empty($itemsPerPage))
        {
            return $this;
        }

        $filterParameters->setOffset(($pageNumber - 1) * $itemsPerPage);

        return $this;
    }

    /**
     * @param FilterParameters $filterParameters
     * @param ChamiloRequest $chamiloRequest
     * @param FieldMapper $fieldMapper
     *
     * @return FilterParametersBuilder
     */
    protected function addSortToFilterParameters(
        FilterParameters $filterParameters, ChamiloRequest $chamiloRequest, FieldMapper $fieldMapper
    )
    {
        $sortField = $chamiloRequest->getFromPostOrUrl(self::PARAM_SORT_FIELD);
        $sortDirection = strtoupper($chamiloRequest->getFromPostOrUrl(self::PARAM_SORT_DIRECTION));

        if (empty($sortField) || empty($sortDirection))
        {
            return $this;
        }

        $allowedDirections = [self::SORT_ASC, self::SORT_DESC];
        if (!in_array($sortDirection, $allowedDirections))
        {
            throw new ValueNotInArrayException(self::PARAM_SORT_DIRECTION, $sortDirection, $allowedDirections);
        }

        $filterParameters->addOrderBy(
            new OrderBy(
                $fieldMapper->getConditionVariableForField($sortField),
                $sortDirection == self::SORT_ASC ? SORT_ASC : SORT_DESC
            )
        );

        return $this;
    }

}
