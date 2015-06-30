<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Blog\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Blog\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

/*
 * To change this template, choose Tools | Templates and open the template in the editor.
 */

/**
 * Description of complex_builderclass
 *
 * @author jevdheyd
 */
class ComplexBuilderComponent extends Manager implements DelegateComponent
{

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
    }
}
