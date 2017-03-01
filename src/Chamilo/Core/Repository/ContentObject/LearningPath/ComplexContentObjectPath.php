<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @package repository\content_object\learning_path
 */
class ComplexContentObjectPath extends \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPath
{

    /**
     *
     * @var DummyLpiAttemptTracker[]
     */
    private $nodes_attempt_data;

    /**
     *
     * @param ContentObject $content_object
     * @param DummyLpiAttemptTracker[] $nodes_attempt_data
     */
    public function __construct(ContentObject $content_object, $nodes_attempt_data = null)
    {
        $this->nodes_attempt_data = $nodes_attempt_data;
        parent::__construct($content_object);
    }

    /**
     *
     * @return DummyLpiAttemptTracker[]
     */
    public function get_nodes_attempt_data()
    {
        return $this->nodes_attempt_data;
    }

    /**
     *
     * @param DummyLpiAttemptTracker[] $nodes_attempt_data
     */
    public function set_nodes_attempt_data($nodes_attempt_data)
    {
        $this->nodes_attempt_data = $nodes_attempt_data;
        foreach($this->get_nodes() as $node)
        {
            $node->set_properties(
                $this->get_properties($node->get_parent_id(), $node->get_complex_content_object_item(), null)
            );
            
            $node->recalculateIsCompleted(false);
        }
    }

    /**
     *
     * @return boolean
     */
    public function has_nodes_attempt_data()
    {
        $data = $this->get_nodes_attempt_data();
        return isset($data);
    }

    /**
     *
     * @return DummyLpiAttemptTracker
     */
    public function get_node_attempt_data(ComplexContentObjectItem $complex_content_object_item)
    {
        $data = $this->get_nodes_attempt_data();
        $id = $complex_content_object_item->get_id();
        return $data[$id];
    }

    /**
     *
     * @param int $parent_id
     * @param ComplexContentObjectItem $complex_content_object_item
     * @param ContentObject $content_object
     * @return multitype:mixed
     */
    public function get_properties($parent_id, $complex_content_object_item, $content_object)
    {
        $properties = array();
        
        if ($this->has_nodes_attempt_data())
        {
            $attempt_data = $this->get_node_attempt_data($complex_content_object_item);
            
            if ($attempt_data)
            {
                $properties[ComplexContentObjectPathNode::PROPERTY_DATA] = $attempt_data;
            }
            else
            {
                $properties[ComplexContentObjectPathNode::PROPERTY_DATA] = null;
            }
        }
        else
        {
            $properties[ComplexContentObjectPathNode::PROPERTY_DATA] = null;
        }
        
        return $properties;
    }

    public function get_progress()
    {
        $completed_steps = 0;
        
        foreach ($this->get_nodes() as $node)
        {
            if ($node->is_completed())
            {
                
                $completed_steps ++;
            }
        }
        
        return round(($completed_steps / $this->count_nodes()) * 100);
    }
}
