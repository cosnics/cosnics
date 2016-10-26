<?php

namespace Chamilo\Core\Home\Interfaces;

/**
 * Interface to describe the necessary functionality for an angular connector
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface AngularConnectorInterface
{
    /**
     * Loads the angular javascript modules and returns them as HTML code
     *
     * @return string
     */
    public function loadAngularModules();

    /**
     * Returns a list of angular modules that must be registered
     *
     * @return string[]
     */
    public function getAngularModules();
}