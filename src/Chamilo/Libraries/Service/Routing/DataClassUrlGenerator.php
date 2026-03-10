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
    protected UrlGenerator $urlGenerator;

    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
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

        return $this->getUrlGenerator()->fromParameters(
            array_merge($parameters, $additionalParameters)
        );
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }
}