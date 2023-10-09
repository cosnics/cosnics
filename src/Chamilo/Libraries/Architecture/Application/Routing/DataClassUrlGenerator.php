<?php
namespace Chamilo\Libraries\Architecture\Application\Routing;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Libraries\Architecture\Application\Routing
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
            Application::PARAM_CONTEXT => $context,
            $actionParameterName => $action,
            $dataClassParameterName => $dataClass->getId()
        ];

        return $this->getUrlGenerator()->fromParameters(
            array_merge($parameters, $additionalParameters)
        );
    }

    public function getBrowseUrl(
        string $context, string $actionParameterName, string $dataClassParameterName, DataClass $dataClass,
        array $additionalParameters = []
    ): string
    {
        return $this->getActionUrl(
            $context, $actionParameterName, $dataClassParameterName, Application::ACTION_BROWSER, $dataClass,
            $additionalParameters
        );
    }

    public function getCreateUrl(
        string $context, string $actionParameterName, string $dataClassParameterName, DataClass $dataClass,
        array $additionalParameters = []
    ): string
    {
        return $this->getActionUrl(
            $context, $actionParameterName, $dataClassParameterName, Application::ACTION_CREATOR, $dataClass,
            $additionalParameters
        );
    }

    public function getDeleteUrl(
        string $context, string $actionParameterName, string $dataClassParameterName, DataClass $dataClass,
        array $additionalParameters = []
    ): string
    {
        return $this->getActionUrl(
            $context, $actionParameterName, $dataClassParameterName, Application::ACTION_DELETER, $dataClass,
            $additionalParameters
        );
    }

    public function getUpdateUrl(
        string $context, string $actionParameterName, string $dataClassParameterName, DataClass $dataClass,
        array $additionalParameters = []
    ): string
    {
        return $this->getActionUrl(
            $context, $actionParameterName, $dataClassParameterName, Application::ACTION_UPDATER, $dataClass,
            $additionalParameters
        );
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

}