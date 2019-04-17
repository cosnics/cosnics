<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Component;

use Chamilo\Application\Weblcms\Bridge\ExternalTool\ExternalToolServiceBridge;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class DisplayComponent extends Manager implements DelegateComponent
{
    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function run()
    {
        $contentObjectPublication = $this->getContentObjectPublication();
        $this->validateAccess($contentObjectPublication);

        $bridge = new ExternalToolServiceBridge();
        $bridge->setContentObjectPublication($contentObjectPublication);
        $bridge->setCourse($this->get_course());
        $bridge->setHasEditRight($this->is_allowed(WeblcmsRights::EDIT_RIGHT, $contentObjectPublication));

        $this->getBridgeManager()->addBridge($bridge);

        $configuration = new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this);

        $applicationFactory = $this->getApplicationFactory();
        $application = $applicationFactory->getApplication(\Chamilo\Core\Repository\ContentObject\ExternalTool\Display\Manager::context(), $configuration);

        return $application->run();
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }
}
