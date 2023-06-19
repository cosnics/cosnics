<?php
namespace Chamilo\Core\Menu\Architecture\Interfaces;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Menu\Architecture\Interfaces
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface SelectableItemInterface
{
    public function isSelected(Item $item, User $user): bool;
}