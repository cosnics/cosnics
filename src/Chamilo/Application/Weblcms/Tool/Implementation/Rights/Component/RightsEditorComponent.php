<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Rights\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Rights\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;

class RightsEditorComponent extends Manager implements BreadcrumbLessComponentInterface
{

    public function run()
    {
        if (!$this->get_course()->is_course_admin($this->get_user()))
        {
            throw new NotAllowedException();
        }

        $request = $this->getRequest();
        $request->query->set(
            \Chamilo\Application\Weblcms\Tool\Action\Manager::PARAM_ACTION,
            \Chamilo\Application\Weblcms\Tool\Action\Manager::RIGHTS_EDITOR_COMPONENT
        );

        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Application\Weblcms\Tool\Action\Manager::CONTEXT,
            new ApplicationConfiguration($request, $this->getUser(), $this)
        )->run();
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID;

        return parent::getAdditionalParameters($additionalParameters);
    }

    public function get_available_rights($location)
    {
        return WeblcmsRights::get_available_rights($location);
    }
}
