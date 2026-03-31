<?php
namespace Chamilo\Libraries\Service\Routing;

use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;

/**
 * @package Chamilo\Libraries\Service\Routing
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DataClassUrlGenerator
{
    public function __construct(protected UrlGenerator $urlGenerator)
    {
    }

    /**
     * @param string[] $additionalParameters
     */
    public function getActionUrl(
        string $context, string $actionParameterName, string $dataClassParameterName, string $action,
        DataClass $dataClass, array $additionalParameters = []
    ): string
    {
        $parameters = [
            ApplicationInterface::PARAM_CONTEXT => $context,
            $actionParameterName => $action,
            $dataClassParameterName => $dataClass->getId()
        ];

        return $this->urlGenerator->fromParameters(array_merge($parameters, $additionalParameters));
    }
}