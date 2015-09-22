<?php
namespace Chamilo\Libraries\Storage\DataClass\Property;

use Chamilo\Libraries\Architecture\Interfaces\Hashable;

/**
 *
 * @package Chamilo\Libraries\Storage\DataClass\Property
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassProperties implements Hashable
{

    private $properties;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty[]
     */
    public function __construct($properties = array())
    {
        $this->properties = (is_array($properties) ? $properties : func_get_args());
    }

    /**
     * Gets the properties
     *
     * @return \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty[]
     */
    public function get()
    {
        return $this->properties;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty $property
     */
    public function add($property)
    {
        $this->properties[] = $property;
    }

    /**
     *
     * @return string
     */
    public function hash()
    {
        $hashes = array();

        foreach ($this->properties as $property)
        {
            $hashes[] = $property->hash();
        }

        sort($hashes);

        return md5(json_encode($hashes));
    }
}
