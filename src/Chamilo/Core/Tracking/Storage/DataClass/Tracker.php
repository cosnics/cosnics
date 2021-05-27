<?php
namespace Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

abstract class Tracker extends DataClass
{

    /**
     *
     * @var Event
     */
    private $event;

    /**
     *
     * @return Event
     */
    public function get_event()
    {
        return $this->event;
    }

    /**
     *
     * @param $event Event
     */
    public function set_event(Event $event)
    {
        $this->event = $event;
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     *
     * @deprecated Use run() instead
     */
    public function track(array $parameters = [])
    {
        return $this->run($parameters);
    }

    abstract public function validate_parameters(array $parameters = []);

    /**
     * Write the values of the properties from the tracker to the database
     *
     * @return boolean
     */
    public function run(array $parameters = [])
    {
        $this->validate_parameters($parameters);
        return $this->create();
    }

    /**
     * Removes tracker items with a given condition
     *
     * @param $condition Condition
     */
    public function remove(Condition $condition = null)
    {
        return DataManager::deletes(static::class, $condition);
    }

    /**
     *
     * @param $type string
     * @param $application string
     * @return Tracker The tracker object
     */
    // public static function factory($type, $context)
    // {
    // $class = $context . '\Storage\DataClass\\' .
    // StringUtilities::getInstance()->createString($type)->upperCamelize();
    // return new $class();
    // }

    /**
     *
     * @param $type string
     * @param $application string
     * @param $condition Condition
     * @param $offset int
     * @param $max_objects int
     * @param $order_by ObjectTableOrder
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator The tracker data resultset
     */
    public static function get_data($class_name, $application, $condition, $offset = null, $max_objects = null,
        $order_by = [])
    {
        return DataManager::retrieves(
            $class_name,
            new DataClassRetrievesParameters($condition, $max_objects, $offset, $order_by));
    }

    /**
     *
     * @param $type string
     * @param $application string
     * @param $condition Condition
     * @param $order_by ObjectTableOrder
     * @return Tracker The tracker
     */
    public static function get_singular_data($class_name, $application, $condition, $order_by = [])
    {
        return DataManager::retrieve($class_name, new DataClassRetrieveParameters($condition, $order_by));
    }

    public static function count_data($class_name, $application, $condition)
    {
        return DataManager::count($class_name, new DataClassCountParameters($condition));
    }
}
