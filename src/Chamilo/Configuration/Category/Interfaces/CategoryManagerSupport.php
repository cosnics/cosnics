<?php
namespace Chamilo\Configuration\Category\Interfaces;

use Chamilo\Configuration\Category\Service\CategoryManagerImplementerInterface;

/**
 * @package Chamilo\Configuration\Category\Interfaces
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface CategoryManagerSupport
{
    public function getCategoryManagerImplementer(): CategoryManagerImplementerInterface;
}
