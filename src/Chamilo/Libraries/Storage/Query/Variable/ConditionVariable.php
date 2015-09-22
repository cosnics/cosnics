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

    public function getHashParts()
    {
        return array(static :: class_name());
    }

    /**
     * Get an md5 representation of this object for identification purposes
     *
     * @return string
     */
    public function hash()
    {
        if (! $this->get_hash())
        {
            $this->set_hash(md5(json_encode($this->getHashParts())));
        }

        return $this->get_hash();
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
