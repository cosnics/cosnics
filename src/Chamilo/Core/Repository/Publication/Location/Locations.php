<?php
namespace Chamilo\Core\Repository\Publication\Location;

use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 * @package Chamilo\Core\Repository\Publication\Location
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Locations
{

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

    public function get_package()
    {
        return ClassnameUtilities::getInstance()->getNamespaceParent($this->get_context());
    }

    public function get_application()
    {
        return ClassnameUtilities::getInstance()->getNamespaceParent($this->get_context(), 5);
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
