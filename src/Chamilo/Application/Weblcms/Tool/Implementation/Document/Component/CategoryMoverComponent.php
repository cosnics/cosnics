<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Document\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Document\Manager;

class CategoryMoverComponent extends Manager
{

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
    }
}
