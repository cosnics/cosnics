<?php
namespace Chamilo\Configuration\Interfaces;

/**
 * @package Chamilo\Configuration\Interfaces
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
interface DataLoaderInterface
{

    public function clearData(): bool;

    /**
     * @return string[]
     */
    public function getData();
}