<?php
namespace Chamilo\Libraries\Format\Table\Interface;

/**
 * @package Chamilo\Libraries\Format\Table\Interface
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface SimpleTableCellRendererModificationInterface
{
    
    public function getModificationLinks(string $data): string;
}
