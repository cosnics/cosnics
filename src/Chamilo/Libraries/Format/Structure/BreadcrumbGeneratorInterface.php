<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 * This class defines the basic layout of the BreadcrumbGenerator class.
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
interface BreadcrumbGeneratorInterface
{
    public function generateBreadcrumbs(Application $application): void;
}