<?php
namespace Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Manager;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\RetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

abstract class Tracker extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    /**
     * @var Event
     */
    private $event;

    /**
     * Write the values of the properties from the tracker to the database
     *
     * @return bool
     */
    public function run(array $parameters = [])
    {
        $this->validate_parameters($parameters);

        return $this->create();
    }

    public static function count_data($class_name, $application, $condition)
    {
        return DataManager::count($class_name, new DataClassCountParameters($condition));
    }

    /**
     * @param $type        string
     * @param $application string
     * @param $condition   Condition
     * @param $offset      int
     * @param $max_objects int
     * @param $order_by    \Chamilo\Libraries\Storage\Query\OrderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection The tracker data resultset
     */
    public static function get_data(
        $class_name, $application, $condition, $offset = null, $max_objects = null, $order_by = null
    )
    {
        return DataManager::retrieves(
            $class_name, new RetrievesParameters($condition, $max_objects, $offset, $order_by)
        );
    }

    /**
     * @return Event
     */
    public function get_event()
    {
        return $this->event;
    }

    /**
     * @param $type        string
     * @param $application string
     * @param $condition   Condition
     * @param $order_by    \Chamilo\Libraries\Storage\Query\OrderBy
     *
     * @return Tracker The tracker
     */
    public static function get_singular_data($class_name, $application, $condition, $order_by = null)
    {
        return DataManager::retrieve($class_name, new RetrieveParameters($condition, $order_by));
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
     * @param $type        string
     * @param $application string
     *
     * @return Tracker The tracker object
     */
    // public static function factory($type, $context)
    // {
    // $class = $context . '\Storage\DataClass\\' .
    // StringUtilities::getInstance()->createString($type)->upperCamelize();
    // return new $class();
    // }

    /**
     * @param $event Event
     */
    public function set_event(Event $event)
    {
        $this->event = $event;
    }

    /**
     * @deprecated Use run() instead
     */
    public function track(array $parameters = [])
    {
        return $this->run($parameters);
    }

    abstract public function validate_parameters(array $parameters = []);
}
