<?php
namespace Chamilo\Core\Repository\Publication\Location;

/**
 *
 * @package core\repository\publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Locations
{
    const CLASS_NAME = __CLASS__;

    /**
     *
     * @var string
     */
    private $context;

    /**
     *
     * @var Location[]
     */
    private $locations = array();

    /**
     *
     * @param string $context
     * @param Location[] $locations
     */
    public function __construct($context, $locations = array())
    {
        $this->context = $context;
        $this->locations = $locations;
    }

    /**
     *
     * @param string $context
     */
    public function set_context($context)
    {
        $this->context = $context;
    }

    /**
     *
     * @return string
     */
    public function get_context()
    {
        return $this->context;
    }

    /**
     *
     * @param Location[] $locations
     */
    public function set_locations(array $locations)
    {
        $this->locations = $locations;
    }

    /**
     *
     * @return Location[]
     */
    public function get_locations()
    {
        return $this->locations;
    }

    /**
     *
     * @param Location $location
     */
    public function add_location($location)
    {
        $this->locations[] = $location;
    }

    /**
     *
     * @return int
     */
    public function size()
    {
        return count($this->locations);
    }
}
