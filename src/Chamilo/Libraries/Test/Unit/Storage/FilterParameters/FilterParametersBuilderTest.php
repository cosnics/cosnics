<?php

namespace Chamilo\Libraries\Test\Unit\Storage\FilterParameters;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Storage\FilterParameters\FieldMapper;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\FilterParameters\FilterParametersBuilder;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Tests the FilterParametersBuilder
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FilterParametersBuilderTest extends ChamiloTestCase
{
    /**
     * @var FilterParametersBuilder
     */
    protected $filterParametersBuilder;

    /**
     * @var FieldMapper
     */
    protected $fieldMapper;

    /**
     * @var ChamiloRequest
     */
    protected $chamiloRequest;

    /**
     * Setup before each test
     */
    public function setUp(): void
    {
        $this->fieldMapper = new FieldMapper();
        $this->fieldMapper->addFieldMapping('username', User::class, User::PROPERTY_USERNAME);

        $this->chamiloRequest = new ChamiloRequest();
        $this->filterParametersBuilder = new FilterParametersBuilder();
    }

    /**
     * Tear down after each test
     */
    public function tearDown(): void
    {
        unset($this->filterParametersBuilder);
    }

    public function testGlobalSearchQuery()
    {
        $this->chamiloRequest->request->set(FilterParametersBuilder::PARAM_GLOBAL_SEARCH_QUERY, 'john');
        $filterParameters = $this->filterParametersBuilder->buildFilterParametersFromRequest(
            $this->chamiloRequest, $this->fieldMapper
        );

        $this->assertEquals('john', $filterParameters->getGlobalSearchQuery());
    }

    public function testFieldsSearchQuery()
    {
        $this->chamiloRequest->request->set(FilterParametersBuilder::PARAM_FIELDS_SEARCH_QUERY, '{"username": "doe"}');
        $filterParameters = $this->filterParametersBuilder->buildFilterParametersFromRequest(
            $this->chamiloRequest, $this->fieldMapper
        );

        $result = $filterParameters->getDataClassSearchQueries()[0];

        /** @var PropertyConditionVariable $propertyConditionVariable */
        $propertyConditionVariable = $result->getConditionVariable();

        $this->assertEquals('doe', $result->getSearchQuery());
        $this->assertEquals(User::class, $propertyConditionVariable->get_class());
        $this->assertEquals(User::PROPERTY_USERNAME, $propertyConditionVariable->get_property());
    }

    public function testFieldsSearchQueryWithUnmappedField()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Field firstname not found in field mapping');

        $this->chamiloRequest->request->set(FilterParametersBuilder::PARAM_FIELDS_SEARCH_QUERY, '{"firstname": "doe"}');
        $this->filterParametersBuilder->buildFilterParametersFromRequest(
            $this->chamiloRequest, $this->fieldMapper
        );
    }

    public function testFieldsSearchQueryWithCorruptJSON()
    {
        $this->chamiloRequest->request->set(FilterParametersBuilder::PARAM_FIELDS_SEARCH_QUERY, '}#aa!çpç&é"}');
        $filterParameters = $this->filterParametersBuilder->buildFilterParametersFromRequest(
            $this->chamiloRequest, $this->fieldMapper
        );

        $this->assertEmpty($filterParameters->getDataClassSearchQueries());
    }

    public function testCount()
    {
        $this->chamiloRequest->request->set(FilterParametersBuilder::PARAM_ITEMS_PER_PAGE, '20');
        $filterParameters = $this->filterParametersBuilder->buildFilterParametersFromRequest(
            $this->chamiloRequest, $this->fieldMapper
        );

        $this->assertEquals(20, $filterParameters->getCount());
    }

    public function testCountWithoutItemsPerPage()
    {
        $filterParameters = $this->filterParametersBuilder->buildFilterParametersFromRequest(
            $this->chamiloRequest, $this->fieldMapper
        );

        $this->assertNull($filterParameters->getCount());
    }

    public function testOffset()
    {
        $this->chamiloRequest->request->set(FilterParametersBuilder::PARAM_PAGE_NUMBER, 5);
        $this->chamiloRequest->request->set(FilterParametersBuilder::PARAM_ITEMS_PER_PAGE, '20');

        $filterParameters = $this->filterParametersBuilder->buildFilterParametersFromRequest(
            $this->chamiloRequest, $this->fieldMapper
        );

        $this->assertEquals(80, $filterParameters->getOffset());
    }

    public function testOffsetWithoutPageNumber()
    {
        $this->chamiloRequest->request->set(FilterParametersBuilder::PARAM_ITEMS_PER_PAGE, '20');

        $filterParameters = $this->filterParametersBuilder->buildFilterParametersFromRequest(
            $this->chamiloRequest, $this->fieldMapper
        );

        $this->assertNull($filterParameters->getOffset());
    }

    public function testOffsetWithoutItemsPerPage()
    {
        $this->chamiloRequest->request->set(FilterParametersBuilder::PARAM_PAGE_NUMBER, 5);

        $filterParameters = $this->filterParametersBuilder->buildFilterParametersFromRequest(
            $this->chamiloRequest, $this->fieldMapper
        );

        $this->assertNull($filterParameters->getOffset());
    }

    public function testSort()
    {
        $this->chamiloRequest->request->set(FilterParametersBuilder::PARAM_SORT_FIELD, 'username');
        $this->chamiloRequest->request->set(FilterParametersBuilder::PARAM_SORT_DIRECTION, 'DESC');

        $filterParameters = $this->filterParametersBuilder->buildFilterParametersFromRequest(
            $this->chamiloRequest, $this->fieldMapper
        );

        $result = $filterParameters->getOrderBy()[0];

        /** @var PropertyConditionVariable $propertyConditionVariable */
        $propertyConditionVariable = $result->getConditionVariable();

        $this->assertEquals(SORT_DESC, $result->getDirection());
        $this->assertEquals(User::class, $propertyConditionVariable->get_class());
        $this->assertEquals(User::PROPERTY_USERNAME, $propertyConditionVariable->get_property());
    }

    public function testSortWithUnmappedSortField()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Field firstname not found in field mapping');

        $this->chamiloRequest->request->set(FilterParametersBuilder::PARAM_SORT_FIELD, 'firstname');
        $this->chamiloRequest->request->set(FilterParametersBuilder::PARAM_SORT_DIRECTION, 'DESC');

        $this->filterParametersBuilder->buildFilterParametersFromRequest(
            $this->chamiloRequest, $this->fieldMapper
        );
    }

    public function testSortWithEmptySortField()
    {
        $this->chamiloRequest->request->set(FilterParametersBuilder::PARAM_SORT_DIRECTION, 'DESC');

        $filterParameters = $this->filterParametersBuilder->buildFilterParametersFromRequest(
            $this->chamiloRequest, $this->fieldMapper
        );

        $this->assertEmpty($filterParameters->getOrderBy());
    }

    public function testSortWithEmptySortDirection()
    {
        $this->chamiloRequest->request->set(FilterParametersBuilder::PARAM_SORT_FIELD, 'username');

        $filterParameters = $this->filterParametersBuilder->buildFilterParametersFromRequest(
            $this->chamiloRequest, $this->fieldMapper
        );

        $this->assertEmpty($filterParameters->getOrderBy());
    }

    public function testSortWithInvalidSortDirection()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The given argument sort_direction with value ADESC is not in the list of allowed values (ASC, DESC)');

        $this->chamiloRequest->request->set(FilterParametersBuilder::PARAM_SORT_FIELD, 'username');
        $this->chamiloRequest->request->set(FilterParametersBuilder::PARAM_SORT_DIRECTION, 'ADESC');

        $this->filterParametersBuilder->buildFilterParametersFromRequest(
            $this->chamiloRequest, $this->fieldMapper
        );
    }
}


