<?php
namespace Chamilo\Application\Portfolio\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * A portfolio publication
 * 
 * @package application\portfolio$Publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Publication extends DataClass
{
    
    // DataClass properties
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_PUBLISHER_ID = 'publisher_id';
    const PROPERTY_PUBLISHED = 'published';
    const PROPERTY_MODIFIED = 'modified';

    /**
     *
     * @var \repository\ContentObject
     */
    private $content_object;

    /**
     *
     * @var \user\User
     */
    private $publisher;

    /**
     *
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_CONTENT_OBJECT_ID, 
                self::PROPERTY_PUBLISHER_ID, 
                self::PROPERTY_PUBLISHED, 
                self::PROPERTY_MODIFIED));
    }

    /**
     *
     * @return int
     */
    public function get_content_object_id()
    {
        return $this->get_default_property(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     *
     * @return ContentObject
     */
    public function get_content_object()
    {
        if (! isset($this->content_object))
        {
            $this->content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $this->get_content_object_id());
        }
        
        return $this->content_object;
    }

    /**
     *
     * @param int $content_object_id
     */
    public function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self::PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    /**
     *
     * @return int
     */
    public function get_publisher_id()
    {
        return $this->get_default_property(self::PROPERTY_PUBLISHER_ID);
    }

    /**
     *
     * @return \user\User
     */
    public function get_publisher()
    {
        if (! isset($this->publisher))
        {
            $this->publisher = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User::class_name(), 
                $this->get_publisher_id());
        }
        
        return $this->publisher;
    }

    /**
     *
     * @param int $publisher_id
     */
    public function set_publisher_id($publisher_id)
    {
        $this->set_default_property(self::PROPERTY_PUBLISHER_ID, $publisher_id);
    }

    /**
     *
     * @return int
     */
    public function get_published()
    {
        return $this->get_default_property(self::PROPERTY_PUBLISHED);
    }

    /**
     *
     * @param int $published
     */
    public function set_published($published)
    {
        $this->set_default_property(self::PROPERTY_PUBLISHED, $published);
    }

    /**
     *
     * @return int
     */
    public function get_modified()
    {
        return $this->get_default_property(self::PROPERTY_MODIFIED);
    }

    /**
     *
     * @param int $modified
     */
    public function set_modified($modified)
    {
        $this->set_default_property(self::PROPERTY_MODIFIED, $modified);
    }

    /**
     * Returns the dependencies for this dataclass
     * 
     * @return \libraries\storage\Condition[string]
     */
    protected function get_dependencies($dependencies = [])
    {
        return array(
            Feedback::class_name() => new EqualityCondition(
                new PropertyConditionVariable(Feedback::class_name(), Feedback::PROPERTY_PUBLICATION_ID), 
                new StaticConditionVariable($this->get_id())));
    }
}
