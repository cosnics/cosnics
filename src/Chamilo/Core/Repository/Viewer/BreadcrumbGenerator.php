<?php
namespace Chamilo\Core\Repository\Viewer;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Platform\Translation;

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
    protected $component;

    /**
     * Generates the breadcrumb for the component name
     */
    protected function generate_component_breadcrumb()
    {
        if ($this->component->areBreadcrumbsDisabled())
        {
            return;
        }
        
        $variable = ClassnameUtilities::getInstance()->getClassNameFromNamespace(get_class($this->component));
        
        $this->breadcrumb_trail->add(
            new Breadcrumb($this->component->get_url(), Translation::get($variable, null, $this->component->package())));
    }
}