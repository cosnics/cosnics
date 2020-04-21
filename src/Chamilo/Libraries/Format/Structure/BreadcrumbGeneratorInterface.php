<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 * This class defines the basic layout of the BreadcrumbGenerator class.
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface BreadcrumbGeneratorInterface
{

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $component
     * @param \Chamilo\Libraries\Format\Structure\BreadcrumbTrail $breadcrumbTrail
     */
    public function __construct(Application $component, BreadcrumbTrail $breadcrumbTrail);

    /**
     * Automatically generates the breadcrumbs based on the given component
     */
    public function generate_breadcrumbs();
}