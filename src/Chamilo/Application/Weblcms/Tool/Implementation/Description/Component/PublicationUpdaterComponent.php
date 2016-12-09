<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Description\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Description\Manager;

class PublicationUpdaterComponent extends Manager
{

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
    }
}
