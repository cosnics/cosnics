<?php
namespace Chamilo\Libraries\Protocol\Ajax\Service;

use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Interface\ApplicationConfigurationInterface;
use Chamilo\Libraries\Protocol\Ajax\Architecture\Domain\JsonAjaxResult;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Libraries\Architecture
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AjaxManager extends Application
{
    /**
     * @param \Chamilo\Libraries\Architecture\Interface\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);
        $this->validateRequest();
    }

    /**
     * @param string[] $postParameters
     *
     * @return string[]
     */
    public function getRequiredPostParameters(array $postParameters = []): array
    {
        return $postParameters;
    }

    protected function handleException($exception): JsonResponse
    {
        $this->getExceptionLogger()->logException($exception);

        return new JsonResponse(null, 500);
    }

    /**
     * Validate the AJAX call, if not validated, trigger an HTTP 400 (Bad request) error
     */
    public function validateRequest(): void
    {
        foreach ($this->getRequiredPostParameters() as $parameter)
        {
            if (!$this->getRequest()->hasRequestOrQuery($parameter))
            {
                JsonAjaxResult::badRequest('Invalid Post parameters');
            }
        }
    }
}
