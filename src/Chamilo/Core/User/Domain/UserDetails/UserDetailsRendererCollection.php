<?php
namespace Chamilo\Core\User\Domain\UserDetails;

use Chamilo\Core\User\Architecture\Interfaces\UserDetailsRendererInterface;
use OutOfBoundsException;

/**
 * @package Chamilo\Core\User\Domain\UserDetails
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserDetailsRendererCollection
{
    /**
     * @var \Chamilo\Core\User\Architecture\Interfaces\UserDetailsRendererInterface[]
     */
    protected array $userDetailsRenderers = [];

    public function addUserDetailsRenderer(UserDetailsRendererInterface $userDetailsRenderer): void
    {
        $this->userDetailsRenderers[get_class($userDetailsRenderer)] = $userDetailsRenderer;
    }

    public function getUserDetailsRenderer(string $userDetailsRendererType): UserDetailsRendererInterface
    {
        if (!array_key_exists($userDetailsRendererType, $this->userDetailsRenderers))
        {
            throw new OutOfBoundsException($userDetailsRendererType . ' is not a valid UserDetailsRenderer');
        }

        return $this->userDetailsRenderers[$userDetailsRendererType];
    }

    /**
     * @return string[]
     */
    public function getUserDetailsRendererTypes(): array
    {
        return array_keys($this->getUserDetailsRenderers());
    }

    /**
     * @return \Chamilo\Core\User\Architecture\Interfaces\UserDetailsRendererInterface[]
     */
    public function getUserDetailsRenderers(): array
    {
        return $this->userDetailsRenderers;
    }
}
