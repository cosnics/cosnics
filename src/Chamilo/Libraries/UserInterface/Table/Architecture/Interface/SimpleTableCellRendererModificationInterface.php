<?php
namespace Chamilo\Libraries\UserInterface\Table\Architecture\Interface;

/**
 * @package Chamilo\Libraries\UserInterface\Table\Architecture\Interface
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface SimpleTableCellRendererModificationInterface
{
    public function getModificationLinks(string $data): string;
}
