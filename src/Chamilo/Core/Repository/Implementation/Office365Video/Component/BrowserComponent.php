<?php
namespace Chamilo\Core\Repository\Implementation\Office365Video\Component;

use Chamilo\Core\Repository\Implementation\Office365Video\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Translation;

class BrowserComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        if ($this->get_external_repository()->get_user_setting($this->get_user_id(), 'session_token'))
        {
            $factory = new ApplicationFactory(
                \Chamilo\Core\Repository\External\Action\Manager::context(), 
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            
            return $factory->run();
        }
        else
        {
            return $this->display_warning_page(Translation::get('YouMustBeLoggedIn'));
        }
    }
}
