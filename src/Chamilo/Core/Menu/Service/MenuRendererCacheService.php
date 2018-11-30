<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Renderer\MenuRenderer;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Psr\SimpleCache\CacheInterface;

class MenuRendererCacheService
{

    /**
     * @var \Chamilo\Core\Menu\Renderer\MenuRenderer
     */
    private $menuRenderer;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    private $userService;

    /**
     * @var \Psr\SimpleCache\CacheInterface
     */
    private $cacheProvider;

    /**
     * @param \Chamilo\Core\Menu\Renderer\MenuRenderer $menuRenderer
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Psr\SimpleCache\CacheInterface $cacheProvider
     */
    public function __construct(MenuRenderer $menuRenderer, UserService $userService, CacheInterface $cacheProvider)
    {
        $this->menuRenderer = $menuRenderer;
        $this->userService = $userService;
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * @return \Chamilo\Core\Menu\Renderer\MenuRenderer
     */
    protected function getMenuRenderer(): MenuRenderer
    {
        return $this->menuRenderer;
    }

    /**
     * @param \Chamilo\Core\Menu\Renderer\MenuRenderer $menuRenderer
     */
    protected function setMenuRenderer(MenuRenderer $menuRenderer): void
    {
        $this->menuRenderer = $menuRenderer;
    }

    /**
     * @return \Chamilo\Core\User\Service\UserService
     */
    protected function getUserService(): UserService
    {
        return $this->userService;
    }

    /**
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    protected function setUserService(UserService $userService): void
    {
        $this->userService = $userService;
    }

    /**
     * @return \Psr\SimpleCache\CacheInterface
     */
    protected function getCacheProvider(): CacheInterface
    {
        return $this->cacheProvider;
    }

    /**
     * @param \Psr\SimpleCache\CacheInterface $cacheProvider
     */
    protected function setCacheProvider(CacheInterface $cacheProvider): void
    {
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * @param string $containerMode
     * @param \Chamilo\Core\User\Storage\DataClass\User|null $user
     *
     * @return string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getMenuForContainerModeAndUser(string $containerMode, User $user = null)
    {
        $cacheKey = md5(serialize(array($containerMode, $user)));

        if (!$this->getCacheProvider()->has($cacheKey))
        {
            $menu = $this->getMenuRenderer()->render($containerMode, $user);

            $this->getCacheProvider()->set($cacheKey, $menu);
        }

        return $this->getCacheProvider()->get($cacheKey);
    }
}