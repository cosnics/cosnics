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
class UserDetailsRendererRegistry extends ArrayCollection
{
    public function __construct(protected readonly Translator $translator)
    {
        parent::__construct();
    }

    public function addUserDetailsRenderer(UserDetailsRendererInterface $userDetailsRenderer): void
    {
        $this->set(get_class($userDetailsRenderer), $userDetailsRenderer);
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function getUserDetailsRenderer(string $userDetailsRendererType): UserDetailsRendererInterface
    {
        if (!$this->containsKey($userDetailsRendererType)) {
            throw new NoSuchClassException($userDetailsRendererType, UserDetailsRendererInterface::class);
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
