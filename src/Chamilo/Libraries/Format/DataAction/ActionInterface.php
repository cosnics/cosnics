<?php

namespace Chamilo\Libraries\Format\DataAction;

/**
 * Interface to describe an action
 *
 * @package Chamilo\Libraries\Format\DataAction
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop - hans.de.bisschop@ehb.be
 */
interface ActionInterface
{
    /**
     * Converts this object's properties to an array
     *
     * @param array $baseArray
     *
     * @return array
     */
    public function toArray($baseArray = []);

    /**
     * Returns a unique name for the action
     *
     * @return string
     */
    public function getName();
}