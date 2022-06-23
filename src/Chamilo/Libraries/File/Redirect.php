<?php
namespace Chamilo\Libraries\File;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 * @package Chamilo\Libraries\File
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @deprecated Use UrlGenerator now
 */
class Redirect
{
    private ?string $anchor;

    /**
     * @var string[]
     */
    private array $filterParameters;

    /**
     * @var string[]
     */
    private array $parameters;

    /**
     * @param string[] $parameters
     * @param string[] $filterParameters
     */
    public function __construct(
        array $parameters = [], array $filterParameters = [], bool $encodeEntities = false, ?string $anchor = null
    )
    {
        $this->parameters = $parameters;
        $this->filterParameters = $filterParameters;
        $this->anchor = $anchor;
    }

    /**
     * @deprecated Use ChamiloRequest::getUri() now
     */
    public function getCurrentUrl(): string
    {
        return $this->getRequest()->getUri();
    }

    protected function getDependencyInjectionContainer(): ContainerInterface
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer();
    }

    protected function getRequest(): ChamiloRequest
    {
        return $this->getDependencyInjectionContainer()->get(ChamiloRequest::class);
    }

    /**
     * @deprecated Use UrlGenerator::fromParameters() now
     */
    public function getUrl(): string
    {
        return $this->getUrlGenerator()->fromParameters(
            $this->parameters, $this->filterParameters, $this->anchor
        );
    }

    protected function getUrlGenerator(): UrlGenerator
    {
        return $this->getDependencyInjectionContainer()->get(UrlGenerator::class);
    }
}
