<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;

/**
 * Parent class for all abstract condition variables used to build conditions for the storage layer
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ConditionVariable implements Hashable
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
    public function get_hash()
    {
        return $this->hash;
    }

    /**
     *
     * @param string $hash
     */
    public function set_hash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * Get an md5 representation of this object for identification purposes
     *
     * @param string[] $hash_parts
     * @return string
     */
    public function hash($hash_parts = array())
    {
        $hash_parts[] = self :: class_name();

        return md5(serialize($hash_parts));
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return static :: context();
    }
}
