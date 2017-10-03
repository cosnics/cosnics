<?php
namespace Chamilo\Libraries\Utilities\Various\MissingIcons;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;

require_once realpath(__DIR__ . '/../../../../../') . '/vendor/autoload.php';

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get('chamilo.libraries.architecture.bootstrap.bootstrap')->setup();

$sizes = array(Theme::ICON_MINI, Theme::ICON_SMALL, Theme::ICON_MEDIUM, Theme::ICON_BIG);

$failures = 0;
$data = array();

$extensions = Filesystem::get_directory_content(
    Path::getInstance()->getResourcesPath('Chamilo\Configuration', false) . 'File' . DIRECTORY_SEPARATOR . 'Extension',
    Filesystem::LIST_DIRECTORIES);

foreach ($extensions as $extension)
{
    $extension = pathinfo($extension);
    $extension = $extension['basename'];

    $data_row = array();
    $data_row[] = $extension;

    $row_failures = 0;

    foreach ($sizes as $size)
    {
        // Regular
        $size_icon_path = Theme::getInstance()->getFileExtension($extension, $size, false);

        if (! file_exists($size_icon_path))
        {
            $row_failures ++;
            $failures ++;
            $data_row[] = '<img src="' . Theme::getInstance()->getCommonImagePath('Error/' . $size) . '" />';
        }
        else
        {
            $data_row[] = '<img src="' . Theme::getInstance()->getFileExtension($extension, $size) . '" />';
        }

        // Not available
        $icon_path = Theme::getInstance()->getFileExtension($extension, $size . 'Na', false);

        if (! file_exists($icon_path))
        {
            if (file_exists($size_icon_path) && Request::get('copy'))
            {
                Filesystem::copy_file($size_icon_path, $icon_path);
            }
            $row_failures ++;
            $failures ++;
            $data_row[] = '<img src="' . Theme::getInstance()->getCommonImagePath('Error/' . $size) . '" />';
        }
        else
        {
            $data_row[] = '<img src="' . Theme::getInstance()->getFileExtension($extension, $size . 'Na') . '" />';
        }

        // New
        $icon_path = Theme::getInstance()->getFileExtension($extension, $size . 'New', false);

        if (! file_exists($icon_path))
        {
            if (file_exists($size_icon_path) && Request::get('copy'))
            {
                Filesystem::copy_file($size_icon_path, $icon_path);
            }
            $row_failures ++;
            $failures ++;
            $data_row[] = '<img src="' . Theme::getInstance()->getCommonImagePath('Error/' . $size) . '" />';
        }
        else
        {
            $data_row[] = '<img src="' . Theme::getInstance()->getFileExtension($extension, $size . 'New') . '" />';
        }
    }

    if ($row_failures > 0 || Request::get('show_all'))
    {
        $data[] = $data_row;
    }
}

$headers = array();
$headers[] = new StaticTableColumn('Application');

foreach ($sizes as $key => $header_size)
{
    $headers[] = new StaticTableColumn(($key * 3) + 1, $header_size);
    $headers[] = new StaticTableColumn(($key * 3) + 2, $header_size . ' NA');
    $headers[] = new StaticTableColumn(($key * 3) + 3, $header_size . ' NEW');
}

$table = new SortableTableFromArray($data, $headers, array(), 0, 200);

$page = Page::getInstance();
$page->setViewMode(Page::VIEW_MODE_HEADERLESS);

$html = array();

if ($failures || Request::get('show_all'))
{
    $html[] = '<h3>' . count($extensions) . ' extensions</h3>';
    $html[] = $table->toHtml();
    $html[] = '<b>Missing icons: ' . $failures . '</b>';

    $total_failures += $failures;
    $total_missing_icons += $failures;
}

$html[] = $page->getHeader()->toHtml();
$html[] = '<style>';
$html[] = 'table td {text-align: center;}';
$html[] = 'table td:first-child {text-align: left;}';
$html[] = 'table th {width: 70px; text-align: center !important;}';
$html[] = 'table th:first-child {width: auto; text-align: left !important;}';
$html[] = '</style>';
$html[] = $page->getFooter()->toHtml();

echo implode(PHP_EOL, $html);