<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class IntegrationLauncherComponent extends Manager
{
    public const PARAM_BASE_CONTEXT = 'BaseContext';

    /**
     * Runs this component and returns it's result
     *
     * @return \Symfony\Component\HttpFoundation\Response|string
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
            $integrationPackage, new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this)
        )->run();
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_BASE_CONTEXT;

        return parent::getAdditionalParameters($additionalParameters);
    }
}
