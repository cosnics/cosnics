<?php
namespace Chamilo\Libraries\Support;

use Chamilo\Libraries\Format\Table\Interfaces\SimpleTableCellRendererInterface;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Support
 */
class DiagnoserCellRenderer implements SimpleTableCellRendererInterface
{

    public function getNamespace(): string
    {
        return StringUtilities::LIBRARIES;
    }

    /**
     * @see \Chamilo\Libraries\Format\Table\Interfaces\SimpleTableCellRendererInterface::get_prefix()
     */
    public function getPrefix(): string
    {
        return '';
    }

    /**
     * @see \Chamilo\Libraries\Format\Table\Interfaces\SimpleTableCellRendererInterface::get_properties()
     */
    public function getProperties(): array
    {
        return ['', 'Section', 'Setting', 'Current', 'Expected', 'Comment'];
    }

    public function renderCell(string $defaultProperty, array $data): string
    {
        $data = $data[$defaultProperty];

        if (is_null($data))
        {
            $data = '-';
        }

        return $data;
    }
}
