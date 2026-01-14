<?php
namespace Chamilo\Core\Menu\Architecture\Interface;

use Chamilo\Core\Menu\Storage\DataClass\Item;

/**
 * @package Chamilo\Core\Menu\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface TranslatableItemInterface
{
    public function determineItemTitleForCurrentLanguage(Item $item);

    public function determineItemTitleForIsoCode(Item $item, string $isoCode): string;
}