<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Rights\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Rights\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class RightsEditorComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        if (! $this->get_course()->is_course_admin($this->get_user()))
        {
            throw new NotAllowedException();
        }

        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Application\Weblcms\Tool\Action\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }

    public function get_available_rights($location)
    {
        return WeblcmsRights :: get_available_rights($location);
    }

    public function get_additional_parameters()
    {
        array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
    }
}
