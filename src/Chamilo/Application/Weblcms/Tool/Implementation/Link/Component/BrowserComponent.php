<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Link\Component;

use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Application\Weblcms\Tool\Implementation\Link\Manager;

class BrowserComponent extends Manager
{

    public function get_publications()
    {
        return $this->get_parent()->get_publications();
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_BROWSE_PUBLICATION_TYPE);
    }
}
