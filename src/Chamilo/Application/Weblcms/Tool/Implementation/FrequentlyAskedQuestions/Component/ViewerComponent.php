<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\FrequentlyAskedQuestions\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\FrequentlyAskedQuestions\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class ViewerComponent extends Manager implements DelegateComponent
{

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
    }
}
