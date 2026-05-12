<?php
namespace Chamilo\Core\Menu\Architecture\Interface;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Storage\Entity\User;

/**
 * @package Chamilo\Core\Menu\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface SelectableItemInterface
{
    public function isSelected(Item $item, User $user): bool;
}