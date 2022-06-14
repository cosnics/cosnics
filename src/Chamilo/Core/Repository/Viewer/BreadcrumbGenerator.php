<?php
namespace Chamilo\Core\Repository\Viewer;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Translation\Translation;

/**
 * Repo Viewer BreadcrumbGenerator
 * 
 * @package common\libraries
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BreadcrumbGenerator extends \Chamilo\Libraries\Format\Structure\BreadcrumbGenerator
{

    /**
     * The application component
     * 
     * @var Manager
     */
    protected $application;

    /**
     * Generates the breadcrumb for the component name
     */
    protected function generate_component_breadcrumb()
    {
        if ($this->application->areBreadcrumbsDisabled())
        {
            return;
        }
        
        $variable = ClassnameUtilities::getInstance()->getClassNameFromNamespace(get_class($this->application));
        
        $this->breadcrumbTrail->add(
            new Breadcrumb($this->application->get_url(), Translation::get($variable, null, $this->application->package())));
    }
}