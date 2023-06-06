<?php
namespace Chamilo\Core\Home\Architecture\Interfaces;

/**
 * Interface to describe the necessary functionality for an angular connector
 *
 * @package Chamilo\Core\Home\Architecture\Interfaces
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
interface AngularConnectorInterface
{

    /**
     * Returns a list of angular modules that must be registered
     *
     * @return string[]
     */
    public function getAngularModules();

    /**
     * Loads the angular javascript modules and returns them as HTML code
     *
     * @return string
     */
    public function loadAngularModules();
}