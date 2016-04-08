<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 * This class defines the basic layout of the BreadcrumbGenerator class.
 *
 * @package common\libraries
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface BreadcrumbGeneratorInterface
{

    /**
     * Constructor
     *
     * @param Application $component
     * @param BreadcrumbTrail $breadcrumb_trail
     */
    public function __construct(Application $component, BreadcrumbTrail $breadcrumb_trail);

    /**
     * **************************************************************************************************************
     * Generate functionality *
     * **************************************************************************************************************
     */

    /**
     * Automatically generates the breadcrumbs based on the given component
     */
    public function generate_breadcrumbs();
}