<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Psr\Log\InvalidArgumentException;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class IntegrationLauncherComponent extends Manager
{
    const PARAM_BASE_CONTEXT = 'BaseContext';

    /**
     * Runs this component and returns it's result
     *
     * @return \Chamilo\Libraries\Format\Response\Response | string
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     */
    public function run()
    {
        $baseContext = $this->getRequest()->getFromUrl(self::PARAM_BASE_CONTEXT);
        if (empty($baseContext))
        {
            throw new NoObjectSelectedException($this->getTranslator()->trans('BaseContext', [], Manager::class));
        }

        $integrationPackage = $baseContext . '\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup';

        return $this->getApplicationFactory()->getApplication(
            $integrationPackage,
            new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this)
        )->run();
    }

    /**
     * @return array
     */
    public function get_additional_parameters()
    {
        return [self::PARAM_BASE_CONTEXT, self::PARAM_COURSE_GROUP];
    }
}
