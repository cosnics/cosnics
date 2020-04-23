<?php
namespace Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax;

use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;

/**
 * Helper class to build an ajax result for an advanced element finder ajax feed
 *
 * @package Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AjaxResultGenerator
{
    const PROPERTY_ELEMENTS = 'elements';
    const PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    /**
     *
     * @var \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax\AjaxResultDataProviderInterface
     */
    protected $ajaxResultDataProvider;

    /**
     *
     * @var string
     */
    protected $searchQuery;

    /**
     *
     * @var integer
     */
    protected $offset;

    /**
     * AdvancedElementFinderAjaxResultGenerator constructor.
     *
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax\AjaxResultDataProviderInterface $ajaxResultDataProvider
     * @param string $searchQuery
     * @param integer $offset
     */
    public function __construct(
        AjaxResultDataProviderInterface $ajaxResultDataProvider, $searchQuery = null, $offset = 0
    )
    {
        $this->setSearchQuery($searchQuery)->setOffset($offset)->setAjaxResultDataProvider($ajaxResultDataProvider);
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\JsonAjaxResult
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
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax\AjaxResultDataProviderInterface
     */
    public function getAjaxResultDataProvider()
    {
        return $this->ajaxResultDataProvider;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax\AjaxResultDataProviderInterface $ajaxResultDataProvider
     */
    public function setAjaxResultDataProvider($ajaxResultDataProvider)
    {
        $this->ajaxResultDataProvider = $ajaxResultDataProvider;
    }

    /**
     *
     * @return integer
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     *
     * @param integer $offset
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax\AjaxResultGenerator
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
     * @param \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[] $searchProperties
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function getSearchCondition($searchProperties = array())
    {
        $condition = null;

        if (!empty($this->searchQuery))
        {
            $searchQueryConditionGenerator = new SearchQueryConditionGenerator();
            $condition = $searchQueryConditionGenerator->getSearchConditions($this->searchQuery, $searchProperties);
        }

        return $condition;
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
     * @param string $searchQuery
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax\AjaxResultGenerator
     */
    public function setSearchQuery($searchQuery)
    {
        $this->searchQuery = $searchQuery;

        return $this;
    }
}