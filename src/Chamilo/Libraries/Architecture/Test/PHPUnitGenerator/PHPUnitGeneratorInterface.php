<?php

namespace Chamilo\Libraries\Architecture\Test\PHPUnitGenerator;

/**
 * Generates the global phpunit configuration file for Chamilo
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface PHPUnitGeneratorInterface
{
    /**
     * Generates the global phpunit configuration file for Chamilo
     */
    public function generate();
}