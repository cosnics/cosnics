<?php
namespace Chamilo\Core\User\Architecture\Domain;

use Chamilo\Core\User\Architecture\Interface\UserDetailsRendererInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserDetailsRendererRegistry
{
    public function __construct(
        protected readonly Translator $translator,
        protected ArrayCollection $userDetailsRenderers = new ArrayCollection()
    )
    {
    }

    public function addUserDetailsRenderer(UserDetailsRendererInterface $userDetailsRenderer): void
    {
        $this->userDetailsRenderers->set(get_class($userDetailsRenderer), $userDetailsRenderer);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function getUserDetailsRenderer(string $userDetailsRendererType): UserDetailsRendererInterface
    {
        if (!$this->userDetailsRenderers->containsKey($userDetailsRendererType)) {
            throw new NoSuchClassException($userDetailsRendererType, UserDetailsRendererInterface::class);
        }

        return $this->userDetailsRenderers->get($userDetailsRendererType);
    }

    /**
     * @return \Chamilo\Core\User\Architecture\Interface\UserDetailsRendererInterface[]
     */
    public function getUserDetailsRenderers(): array
    {
        return $this->userDetailsRenderers->toArray();
    }
}
