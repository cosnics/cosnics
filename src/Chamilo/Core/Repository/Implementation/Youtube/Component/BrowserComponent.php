<?php
namespace Chamilo\Core\Repository\Implementation\Youtube\Component;

use Chamilo\Core\Repository\Implementation\Youtube\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Session\Request;

class BrowserComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        if (Request :: get(Manager :: PARAM_FEED_TYPE) == Manager :: FEED_STANDARD_TYPE)
        {
            $this->set_parameter(Manager :: PARAM_FEED_IDENTIFIER, Request :: get(Manager :: PARAM_FEED_IDENTIFIER));
        }

        return parent :: run();
    }
}
