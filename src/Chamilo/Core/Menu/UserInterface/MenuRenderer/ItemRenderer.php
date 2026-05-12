<?php
namespace Chamilo\Core\Menu\UserInterface\MenuRenderer;

use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\UserInterface\MenuRenderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract readonly class ItemRenderer
{
    public function __construct(
        protected Translator $translator, protected CachedItemService $itemCacheService,
        protected ChamiloRequest $request
    )
    {
    }

    abstract public function render(Item $item, User $user): string;

    /**
     * @param string[] $existingClasses
     *
     * @return string[]
     */
    protected function getClasses(bool $isSelected = false, array $existingClasses = []): array
    {
        if ($isSelected) {
            $existingClasses[] = 'active';
        }

        return $existingClasses;
    }

    abstract public function getRendererTypeGlyph(): InlineGlyph;

    abstract public function getRendererTypeName(): string;

    abstract public function renderTitleForCurrentLanguage(Item $item): string;

    abstract public function renderTitleForIsocode(Item $item, string $isoCode): string;
}