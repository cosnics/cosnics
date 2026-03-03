<?php
namespace Chamilo\Core\User\Architecture\Domain;

use Chamilo\Core\User\Architecture\Interface\UserDetailsRendererInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OutOfBoundsException;

/**
 * @package Chamilo\Core\User\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserDetailsRendererRegistry extends ArrayCollection
{
    public function addUserDetailsRenderer(UserDetailsRendererInterface $userDetailsRenderer): void
    {
        $this->set(get_class($userDetailsRenderer), $userDetailsRenderer);
    }

    public function getUserDetailsRenderer(string $userDetailsRendererType): UserDetailsRendererInterface
    {
        if (!$this->containsKey($userDetailsRendererType)) {
            throw new OutOfBoundsException($userDetailsRendererType . ' is not a valid UserDetailsRenderer');
        }

        return $this->get($userDetailsRendererType);
    }

    /**
     * @return string[]
     */
    public function getUserDetailsRendererTypes(): array
    {
        return $this->getKeys();
    }

    /**
     * @return \Chamilo\Core\User\Architecture\Interface\UserDetailsRendererInterface[]
     */
    public function getUserDetailsRenderers(): array
    {
        return $this->toArray();
    }
}
