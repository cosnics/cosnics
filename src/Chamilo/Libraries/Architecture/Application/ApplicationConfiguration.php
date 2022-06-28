<?php
namespace Chamilo\Libraries\Architecture\Application;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\ChamiloRequest;

/**
 * @package Chamilo\Libraries\Architecture\Application
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ApplicationConfiguration implements ApplicationConfigurationInterface
{

    private ?Application $application;

    /**
     *
     * @var string[]
     */
    private array $configurationParameters;

    private ChamiloRequest $request;

    private ?User $user;

    /**
     * @param string[] $configurationParameters
     */
    public function __construct(
        ChamiloRequest $request, ?User $user = null, ?Application $parentApplication = null,
        array $configurationParameters = []
    )
    {
        $this->request = $request;
        $this->user = $user;
        $this->application = $parentApplication;
        $this->configurationParameters = $configurationParameters;
    }

    public function get(string $key, ?string $defaultValue = null): string
    {
        return $this->configurationParameters[$key] ?? $defaultValue;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): ApplicationConfigurationInterface
    {
        $this->user = $user;

        return $this;
    }

    public function set(string $key, string $value): ApplicationConfigurationInterface
    {
        $this->configurationParameters[$key] = $value;

        return $this;
    }
}
