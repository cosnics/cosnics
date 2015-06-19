<?php
namespace Chamilo\Core\Repository\Publication\Location;

use Chamilo\Core\Repository\Publication\LocationSupport;

/**
 *
 * @package core\repository\publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Location implements LocationSupport
{

    /**
     *
     * @var string
     */
    private $context;

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @param string $context
     * @param string $name
     */
    function __construct($context, $name)
    {
        $this->context = $context;
        $this->name = $name;
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
    public function get_name()
    {
        return $this->name;
    }

    /**
     *
     * @param string $name
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @see \core\repository\publication\LocationSupport::encode()
     */
    public function encode()
    {
        return base64_encode(serialize($this));
    }
}