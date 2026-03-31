<?php
namespace Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Domain;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionRendererInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * @package Chamilo\Libraries\Protocol\Error\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserExceptionRendererRegistry
{
    public function __construct(protected ArrayCollection $userExceptionRenderers = new ArrayCollection())
    {
    }

    public function addUserExceptionRenderer(UserExceptionRendererInterface $userExceptionRenderer): void
    {
        $this->userExceptionRenderers->set(get_class($userExceptionRenderer), $userExceptionRenderer);
    }

    /**
     * @template tGetUserExceptionRenderer
     * @param class-string<tGetUserExceptionRenderer> $userExceptionRendererClassName
     *
     * @return tGetUserExceptionRenderer|UserExceptionRendererInterface
     * @throws \Exception
     */
    public function getUserExceptionRenderer(string $userExceptionRendererClassName): UserExceptionRendererInterface
    {
        if (!$this->userExceptionRenderers->containsKey($userExceptionRendererClassName)) {
            throw new Exception($userExceptionRendererClassName . ' is not a valid UserExceptionRendererInterface');
        }

        return $this->userExceptionRenderers->get($userExceptionRendererClassName);
    }

    /**
     * @throws \Exception
     */
    public function getUserExceptionRendererForUserException(UserExceptionInterface $userException
    ): UserExceptionRendererInterface
    {
        return $this->getUserExceptionRenderer($userException->getUserExceptionRendererClassName());
    }
}