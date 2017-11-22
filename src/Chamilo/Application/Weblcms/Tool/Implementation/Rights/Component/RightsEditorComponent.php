<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Rights\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Rights\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class RightsEditorComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        if (! $this->get_course()->is_course_admin($this->get_user()))
        {
            throw new NotAllowedException();
        }

        $request = $this->getRequest();
        $request->query->set(
            \Chamilo\Application\Weblcms\Tool\Action\Manager::PARAM_ACTION,
            \Chamilo\Application\Weblcms\Tool\Action\Manager::RIGHTS_EDITOR_COMPONENT);

        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Application\Weblcms\Tool\Action\Manager::context(),
            new ApplicationConfiguration($request, $this->get_user(), $this))->run();
    }

    public function get_available_rights($location)
    {
        return WeblcmsRights::get_available_rights($location);
    }

    public function get_additional_parameters()
    {
        array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }
}
