<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Manager;
use Chamilo\Core\Home\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\ExternalTool\Display\DisplayParameters;
use Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Factory\ApplicationFactory;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class DisplayComponent extends Manager
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

        $contentObject = $contentObjectPublication->get_content_object();
        if (!$contentObject instanceof ExternalTool)
        {
            throw new \RuntimeException(
                'The given publication does not reference a valid external tool and can therefor not be displayed'
            );
        }

        $parameters = new DisplayParameters();
        $parameters->setExternalTool($contentObject);

        $configuration = new ApplicationConfiguration(
            $this->getRequest(), $this->getUser(), $this,
            [\Chamilo\Core\Repository\ContentObject\ExternalTool\Display\Manager::CONFIG_DISPLAY_PARAMETERS => $parameters]
        );

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
