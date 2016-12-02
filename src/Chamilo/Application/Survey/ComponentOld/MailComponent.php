<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Application\Survey\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class MailComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $factory = new ApplicationFactory(
            \Chamilo\Application\Survey\Mail\Manager::context(), 
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        
        // $component = $factory->getComponent();
        // $component->set_parameter(self :: PARAM_PUBLICATION_ID, $this->publication_id);
        
        return $factory->run();
    }
}
?>