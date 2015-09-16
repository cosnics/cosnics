<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Component;

use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager;

class CategoryMoverComponent extends Manager implements DelegateComponent
{

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
    }
}
