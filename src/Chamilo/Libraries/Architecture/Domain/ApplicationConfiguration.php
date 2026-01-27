<?php
namespace Chamilo\Libraries\Architecture\Domain;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationConfigurationInterface;
use Chamilo\Libraries\Platform\ChamiloRequest;

/**
 * @package Chamilo\Libraries\Architecture\Application
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ApplicationConfiguration implements ApplicationConfigurationInterface
{

    private ChamiloRequest $request;

    private ?User $user;

    public function __construct(ChamiloRequest $request, ?User $user = null)
    {
        $this->request = $request;
        $this->user = $user;
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
}
