<?php
namespace Chamilo\Libraries\Format\Table\Interfaces;

/**
 *
 * @package Chamilo\Libraries\Format\Table\Interfaces
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface SimpleTableCellRendererInterface
{

    public function getPrefix(): string;

    /**
     * @return string[]
     */
    public function getProperties(): array;

    /**
     * @param string[] $data
     */
    public function renderCell(string $defaultProperty, array $data): string;
}
