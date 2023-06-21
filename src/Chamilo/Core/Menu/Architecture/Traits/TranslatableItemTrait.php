<?php
namespace Chamilo\Core\Menu\Architecture\Traits;

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
    protected array $fallbackIsoCodes;

    public function determineItemTitleForCurrentLanguage(Item $item): string
    {
        return $this->determineItemTitleForIsoCode($item, $this->getTranslator()->getLocale());
    }

    public function determineItemTitleForIsoCode(Item $item, string $isoCode): string
    {
        if ($item->getTitleForIsoCode($isoCode))
        {
            return $item->getTitleForIsoCode($isoCode);
        }
        else
        {
            $fallbackIsoCodes = $this->getFallbackIsoCodes();

            foreach ($fallbackIsoCodes as $fallbackIsoCode)
            {
                if ($item->getTitleForIsoCode($fallbackIsoCode))
                {
                    return $item->getTitleForIsoCode($fallbackIsoCode);
                }
            }
        }

        return $this->getTranslator()->trans('MenuItem', [], Manager::CONTEXT);
    }

    /**
     * @return string[]
     */
    protected function getFallbackIsoCodes(): array
    {
        return $this->fallbackIsoCodes;
    }

    abstract public function getTranslator(): Translator;

}