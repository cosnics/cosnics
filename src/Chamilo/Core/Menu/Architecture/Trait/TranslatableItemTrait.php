<?php
namespace Chamilo\Core\Menu\Architecture\Trait;

use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Architecture\Traits
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait TranslatableItemTrait
{
    /**
     * @var string[]
     */
    protected readonly array $fallbackIsoCodes;

    protected readonly Translator $translator;

    public function determineItemTitleForCurrentLanguage(Item $item): string
    {
        return $this->determineItemTitleForIsoCode($item, $this->translator->getLocale());
    }

    public function determineItemTitleForIsoCode(Item $item, string $isoCode): string
    {
        if ($item->getTitleForIsoCode($isoCode)) {
            return $item->getTitleForIsoCode($isoCode);
        }
        else {
            foreach ($this->fallbackIsoCodes as $fallbackIsoCode) {
                if ($item->getTitleForIsoCode($fallbackIsoCode)) {
                    return $item->getTitleForIsoCode($fallbackIsoCode);
                }
            }
        }

        return $this->translator->trans('MenuItem', [], Manager::CONTEXT);
    }
}