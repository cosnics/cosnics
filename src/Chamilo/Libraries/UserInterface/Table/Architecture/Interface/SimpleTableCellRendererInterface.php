<?php
namespace Chamilo\Libraries\UserInterface\Table\Architecture\Interface;

/**
 * @package Chamilo\Libraries\Format\Table\Interface
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface SimpleTableCellRendererInterface
{

    public function getNamespace(): string;

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
