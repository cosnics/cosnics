<?php
namespace Chamilo\Libraries\Utilities\Various\MissingIcons;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;

require_once __DIR__ . '/../../../Architecture/Bootstrap.php';
\Chamilo\Libraries\Architecture\Bootstrap :: getInstance()->setup();

$sizes = array(Theme :: ICON_MINI, Theme :: ICON_SMALL, Theme :: ICON_MEDIUM, Theme :: ICON_BIG);

$html = array();
$failures = 0;
$data = array();

$extensions = Filesystem :: get_directory_content(
    Theme :: getInstance()->getCommonImagesPath(false) . 'file/extension',
    Filesystem :: LIST_DIRECTORIES);

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
        $size_icon_path = Theme :: getInstance()->getCommonImagesPath(false) . 'file/extension/' . $extension . '/' . $size . '.png';

        if (! file_exists($size_icon_path))
        {
            $row_failures ++;
            $failures ++;
            $data_row[] = '<img src="' . Theme :: getInstance()->getCommonImagesPath() . 'error/' . $size . '.png" />';
        }
        else
        {
            $data_row[] = '<img src="' . Theme :: getInstance()->getCommonImagesPath() . 'file/extension/' . $extension . '/' . $size .
                 '.png" />';
        }

        // Not available
        $icon_path = Theme :: getInstance()->getCommonImagesPath(false) . 'file/extension/' . $extension . '/' . $size . '_na.png';

        if (! file_exists($icon_path))
        {
            if (file_exists($size_icon_path) && Request :: get('copy'))
            {
                Filesystem :: copy_file($size_icon_path, $icon_path);
            }
            $row_failures ++;
            $failures ++;
            $data_row[] = '<img src="' . Theme :: getInstance()->getCommonImagesPath() . 'error/' . $size . '.png" />';
        }
        else
        {
            $data_row[] = '<img src="' . Theme :: getInstance()->getCommonImagesPath() . 'file/extension/' . $extension . '/' . $size .
                 '_na.png" />';
        }

        // New
        $icon_path = Theme :: getInstance()->getCommonImagesPath(false) . 'file/extension/' . $extension . '/' . $size . '_new.png';

        if (! file_exists($icon_path))
        {
            if (file_exists($size_icon_path) && Request :: get('copy'))
            {
                Filesystem :: copy_file($size_icon_path, $icon_path);
            }
            $row_failures ++;
            $failures ++;
            $data_row[] = '<img src="' . Theme :: getInstance()->getCommonImagesPath() . 'error/' . $size . '.png" />';
        }
        else
        {
            $data_row[] = '<img src="' . Theme :: getInstance()->getCommonImagesPath() . 'file/extension/' . $extension . '/' . $size .
                 '_new.png" />';
        }
    }

    if ($row_failures > 0 || Request :: get('show_all'))
    {
        $data[] = $data_row;
    }
}

$table = new SortableTableFromArray($data, 0, 200);
$header = $table->getHeader();
$table->set_header(0, 'Application');

foreach ($sizes as $key => $header_size)
{
    $table->set_header(($key * 3) + 1, $header_size);
    $table->set_header(($key * 3) + 2, $header_size . ' NA');
    $table->set_header(($key * 3) + 3, $header_size . ' NEW');
}

if ($failures || Request :: get('show_all'))
{
    $html[] = '<h3>' . count($extensions) . ' extensions</h3>';
    $html[] = $table->as_html();
    $html[] = '<b>Missing icons: ' . $failures . '</b>';

    $total_failures += $failures;
    $total_missing_icons += $failures;
}

Display :: small_header();

$html[] = '<style>';
$html[] = 'table td {text-align: center;}';
$html[] = 'table td:first-child {text-align: left;}';
$html[] = 'table th {width: 70px; text-align: center !important;}';
$html[] = 'table th:first-child {width: auto; text-align: left !important;}';
$html[] = '</style>';

echo implode(PHP_EOL, $html);

Display :: small_footer();
