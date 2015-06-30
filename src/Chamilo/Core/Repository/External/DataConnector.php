<?php
namespace Chamilo\Core\Repository\External;

use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;

/**
 *
 * @author Hans De Bisschop
 */
abstract class DataConnector
{

    /**
     *
     * @var array
     */
    private static $instances = array();

    /**
     *
     * @var ExternalRepository
     */
    private $external_repository_instance;

    /**
     *
     * @param $external_repository_instance ExternalRepository
     */
    public function __construct($external_repository_instance)
    {
        $this->external_repository_instance = $external_repository_instance;
    }

    /**
     *
     * @return ExternalRepository
     */
    public function get_external_repository_instance()
    {
        return $this->external_repository_instance;
    }

    /**
     *
     * @param $external_repository_instance ExternalRepository
     */
    public function set_external_repository_instance($external_repository_instance)
    {
        $this->external_repository_instance = $external_repository_instance;
    }

    /**
     *
     * @return int
     */
    public function get_external_repository_instance_id()
    {
        return $this->get_external_repository_instance()->get_id();
    }

    /**
     *
     * @param $external_repository ExternalInstance
     * @return DataConnector
     */
    public static function factory($external_instance)
    {
        $class = $external_instance->get_implementation() . '\DataConnector';
        return new $class($external_instance);
    }

    /**
     *
     * @param $external_repository_instance ExternalRepository
     * @return DataConnector
     */
    public static function get_instance($external_repository_instance)
    {
        if (! isset(self :: $instances[$external_repository_instance->get_id()]))
        {
            self :: $instances[$external_repository_instance->get_id()] = self :: factory($external_repository_instance);
        }
        return self :: $instances[$external_repository_instance->get_id()];
    }

    /**
     *
     * @param $id string
     */
    abstract public function retrieve_external_repository_object($id);

    public function retrieve_external_object(SynchronizationData $external_sync)
    {
        return $this->retrieve_external_repository_object($external_sync->get_external_object_id());
    }

    /**
     *
     * @param $condition mixed
     * @param $order_property ObjectTableOrder
     * @param $offset int
     * @param $count int
     */
    abstract public function retrieve_external_repository_objects($condition, $order_property, $offset, $count);

    /**
     *
     * @param $condition mixed
     */
    abstract public function count_external_repository_objects($condition);

    /**
     *
     * @param $id string
     */
    abstract public function delete_external_repository_object($id);

    /**
     *
     * @param $id string
     */
    abstract public function export_external_repository_object($id);

    /**
     *
     * @param $query string
     */
    abstract public static function translate_search_query($query);

    /**
     *
     * @return array
     */
    public static function get_sort_properties()
    {
        return array();
    }
}
