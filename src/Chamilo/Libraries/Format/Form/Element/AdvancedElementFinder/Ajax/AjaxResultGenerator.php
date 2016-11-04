<?php
namespace Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax;

use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Helper class to build an ajax result for an advanced element finder ajax feed
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AjaxResultGenerator
{
    const PROPERTY_ELEMENTS = 'elements';
    const PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    /**
     *
     * @var AjaxResultDataProviderInterface
     */
    protected $ajaxResultDataProvider;

    /**
     *
     * @var string
     */
    protected $searchQuery;

    /**
     *
     * @var int
     */
    protected $offset;

    /**
     * AdvancedElementFinderAjaxResultGenerator constructor.
     *
     * @param AjaxResultDataProviderInterface $ajaxResultDataProvider
     * @param string $searchQuery
     * @param int $offset
     */
    public function __construct(AjaxResultDataProviderInterface $ajaxResultDataProvider, $searchQuery = null, $offset = 0)
    {
        $this->setSearchQuery($searchQuery)->setOffset($offset)->setAjaxResultDataProvider($ajaxResultDataProvider);
    }

    /**
     * Generates the ajax result
     */
    public function generateAjaxResult()
    {
        $result = new JsonAjaxResult();

        $elements = new AdvancedElementFinderElements();
        $this->ajaxResultDataProvider->generateElements($elements);
        $elements = $elements->as_array();

        $result->set_property(self::PROPERTY_ELEMENTS, $elements);
        $result->set_property(self::PROPERTY_TOTAL_ELEMENTS, $this->ajaxResultDataProvider->getTotalNumberOfElements());

        return $result;
    }

    /**
     *
     * @param PropertyConditionVariable[] $searchProperties
     *
     * @return Condition
     */
    public function getSearchCondition($searchProperties = array())
    {
        $condition = null;

        if (! empty($this->searchQuery))
        {
            $condition = Utilities::query_to_condition($this->searchQuery, $searchProperties);
        }

        return $condition;
    }

    /**
     *
     * @param int $offset
     *
     * @return $this
     */
    public function setOffset($offset = 0)
    {
        if (empty($offset))
        {
            $offset = 0;
        }

        $this->offset = $offset;

        return $this;
    }

    /**
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     *
     * @param string $searchQuery
     *
     * @return $this
     */
    public function setSearchQuery($searchQuery)
    {
        $this->searchQuery = $searchQuery;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getSearchQuery()
    {
        return $this->searchQuery;
    }

    /**
     *
     * @return AjaxResultDataProviderInterface
     */
    public function getAjaxResultDataProvider()
    {
        return $this->ajaxResultDataProvider;
    }

    /**
     *
     * @param AjaxResultDataProviderInterface $ajaxResultDataProvider
     */
    public function setAjaxResultDataProvider($ajaxResultDataProvider)
    {
        $this->ajaxResultDataProvider = $ajaxResultDataProvider;
    }
}