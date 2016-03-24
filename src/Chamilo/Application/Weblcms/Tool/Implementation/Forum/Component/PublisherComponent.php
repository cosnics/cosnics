<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Forum\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Forum\Manager;

class PublisherComponent extends Manager
{

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ID,
            \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ACTION);
    }
}
