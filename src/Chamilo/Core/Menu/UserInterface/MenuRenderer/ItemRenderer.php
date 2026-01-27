<?php
namespace Chamilo\Core\Menu\UserInterface\MenuRenderer;

use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\UserInterface\MenuRenderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class ItemRenderer
{
    private CachedItemService $itemCacheService;

    private ChamiloRequest $request;

    private Translator $translator;

    public function __construct(Translator $translator, CachedItemService $itemCacheService, ChamiloRequest $request)
    {
        $this->translator = $translator;
        $this->itemCacheService = $itemCacheService;
        $this->request = $request;
    }

    abstract public function render(Item $item, User $user): string;

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

    abstract public function getRendererTypeGlyph(): InlineGlyph;

    abstract public function getRendererTypeName(): string;

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    abstract public function renderTitleForCurrentLanguage(Item $item): string;

    abstract public function renderTitleForIsocode(Item $item, string $isoCode): string;
}