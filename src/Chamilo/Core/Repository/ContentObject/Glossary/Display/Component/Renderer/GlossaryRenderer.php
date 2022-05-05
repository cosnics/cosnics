<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Basic class to render the glossary
 * 
 * @package repository\content_object\glossary
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class GlossaryRenderer
{

    /**
     * The component in which this renderer runs
     * 
     * @var mixed
     */
    private $component;

    /**
     * The glossary
     * 
     * @var Glossary
     */
    private $glossary;

    /**
     * The search query
     * 
     * @var string
     */
    private $search_query;

    /**
     * Constructor
     * 
     * @param mixed $component
     * @param Glossary $glossary
     * @param string $search_query
     */
    public function __construct($component, $glossary, $search_query)
    {
        $this->component = $component;
        $this->glossary = $glossary;
        $this->search_query = $search_query;
    }

    /**
     * Retrieves the objects from the database
     * 
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $order_property
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem>
     */
    public function get_objects($offset = null, $count = null, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters(
            $this->get_condition(), 
            $count, 
            $offset, 
            $order_property, 
            $this->get_joins());
        
        return DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class,
            $parameters);
    }

    /**
     * Counts the objects from the database
     * 
     * @return int
     */
    public function count_objects()
    {
        $parameters = new DataClassCountParameters($this->get_condition(), $this->get_joins());
        
        return DataManager::count_complex_content_object_items(
            ComplexContentObjectItem::class,
            $parameters);
    }

    /**
     * Returns the joins with the content object
     * 
     * @return Joins
     */
    protected function get_joins()
    {
        $joins = new Joins();
        
        $joins->add(
            new Join(
                ContentObject::class,
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ComplexContentObjectItem::class,
                        ComplexContentObjectItem::PROPERTY_REF), 
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID))));
        
        return $joins;
    }

    /**
     * Returns the condition for the children
     * 
     * @return Condition
     */
    protected function get_condition()
    {
        $query = $this->search_query;
        
        if (isset($query) && $query != '')
        {
            $search_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE),
                $query );
            
            $search_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION),
                $query);
            
            $conditions[] = new OrCondition($search_conditions);
        }
        
        $objects = $this->glossary;
        
        if (! is_array($objects))
        {
            $objects = array($objects);
        }
        
        $co_conditions = [];
        
        foreach ($objects as $object)
        {
            $co_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class,
                    ComplexContentObjectItem::PROPERTY_PARENT), 
                new StaticConditionVariable($object->get_id()));
        }
        
        $conditions[] = new OrCondition($co_conditions);
        
        $condition = new AndCondition($conditions);
        
        return $condition;
    }

    /**
     * Returns the glossary
     * 
     * @return Glossary
     */
    public function get_glossary()
    {
        return $this->glossary;
    }

    /**
     * Returns the search query
     * 
     * @return string
     */
    public function get_search_query()
    {
        return $this->search_query;
    }

    /**
     * Returns the component
     * 
     * @return mixed
     */
    public function get_component()
    {
        return $this->component;
    }

    /**
     * Renders the glossary
     * 
     * @return string
     */
    abstract public function render();
}