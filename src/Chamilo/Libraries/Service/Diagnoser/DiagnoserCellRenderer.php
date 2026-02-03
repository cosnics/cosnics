<?php
namespace Chamilo\Libraries\Service\Diagnoser;

use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Table\Architecture\Interface\SimpleTableCellRendererInterface;

/**
 * @package Chamilo\Libraries\Service\Diagnoser
 */
class DiagnoserCellRenderer implements SimpleTableCellRendererInterface
{
    public function getNamespace(): string
    {
        return StringUtilities::LIBRARIES;
    }

    /**
     * @see \Chamilo\Libraries\UserInterface\Table\Architecture\Interface\SimpleTableCellRendererInterface::get_prefix()
     */
    public function getPrefix(): string
    {
        return '';
    }

    /**
     * @see \Chamilo\Libraries\UserInterface\Table\Architecture\Interface\SimpleTableCellRendererInterface::get_properties()
     */
    public function getProperties(): array
    {
        return ['', 'Section', 'Setting', 'Current', 'Expected', 'Comment'];
    }

    public function renderCell(string $defaultProperty, array $data): string
    {
        $data = $data[$defaultProperty];

        if (is_null($data)) {
            $data = '-';
        }

        return $data;
    }
}
