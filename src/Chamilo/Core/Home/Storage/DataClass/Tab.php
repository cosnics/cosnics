<?php
namespace Chamilo\Core\Home\Storage\DataClass;

use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Storage\DataManager;

/**
 * @package Chamilo\Core\Home\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Tab extends Element
{
    public const CONTEXT = Manager::CONTEXT;

    public function canBeDeleted()
    {
        $blocks = DataManager::retrieveTabBlocks($this);

        foreach ($blocks as $block)
        {
            if ($block->getContext() == 'Chamilo\Core\Admin' || $block->getContext() == 'Chamilo\Core\User')
            {
                return false;
            }
        }

        return true;
    }
}
