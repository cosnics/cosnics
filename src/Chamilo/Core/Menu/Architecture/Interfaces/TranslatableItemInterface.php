<?php
namespace Chamilo\Core\Menu\Architecture\Interfaces;

use Chamilo\Core\Menu\Storage\DataClass\Item;

/**
 * @package Chamilo\Core\Menu\Architecture\Interfaces
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface TranslatableItemInterface
{
    public function determineItemTitleForCurrentLanguage(Item $item);
}