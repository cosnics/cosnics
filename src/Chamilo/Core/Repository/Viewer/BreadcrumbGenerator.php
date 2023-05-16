<?php
namespace Chamilo\Core\Repository\Viewer;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Translation\Translation;

/**
 * Repo Viewer BreadcrumbGenerator
 *
 * @package common\libraries
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class BreadcrumbGenerator extends \Chamilo\Libraries\Format\Structure\BreadcrumbGenerator
{

    /**
     * Generates the breadcrumb for the component name
     */
    protected function generateComponentBreadcrumb()
    {
        if ($this->getApplication()->areBreadcrumbsDisabled())
        {
            return;
        }

        $variable = ClassnameUtilities::getInstance()->getClassNameFromNamespace(get_class($this->getApplication()));

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb(
                $this->getApplication()->get_url(), Translation::get(
                $variable, null, $this->getApplication()::CONTEXT
            )
            )
        );
    }
}