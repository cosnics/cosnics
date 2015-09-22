<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;

/**
 * Parent class for all abstract conditions used to retrieve objects from the storage layer
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package common.libraries
 */
abstract class Condition implements Hashable
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;

    /**
     *
     * @var string
     */
    private $hash;

    /**
     *
     * @return string
     */
    function get_hash()
    {
        return $this->hash;
    }

    /**
     *
     * @param string $hash
     */
    function set_hash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * Get an md5 representation of this object for identification purposes
     *
     * @param $hash_parts multitype:string
     * @return string
     */
    public function hash($hash_parts = array())
    {
        $hash_parts[] = self :: class_name();

        return md5(serialize($hash_parts));
    }

    /**
     * @return string
     */
    public static function package()
    {
        return static :: context();
    }
}
