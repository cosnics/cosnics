<?php
namespace Chamilo\Core\Menu\Service\Renderer;

use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Service\Renderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class ItemRenderer
{
    private AuthorizationCheckerInterface $authorizationChecker;

    private CachedItemService $itemCacheService;

    private ChamiloRequest $request;

    private Translator $translator;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator,
        CachedItemService $itemCacheService, ChamiloRequest $request
    )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->translator = $translator;
        $this->itemCacheService = $itemCacheService;
        $this->request = $request;
    }

    abstract public function render(Item $item, User $user): string;

    public function getAuthorizationChecker(): AuthorizationCheckerInterface
    {
        return $this->authorizationChecker;
    }

    /**
     * @param string[] $existingClasses
     *
     * @return string[]
     */
    protected function getClasses(bool $isSelected = false, array $existingClasses = []): array
    {
        if ($isSelected)
        {
            $existingClasses[] = 'active';
        }

        return $existingClasses;
    }

    public function getItemCacheService(): CachedItemService
    {
        return $this->itemCacheService;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}