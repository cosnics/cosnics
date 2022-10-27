<?php
namespace Chamilo\Libraries\Format\Table\Interfaces;

/**
 * @package Chamilo\Libraries\Format\Table\Interfaces
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface SimpleTableCellRendererModificationInterface
{
    
    public function getModificationLinks(string $data): string;
}
