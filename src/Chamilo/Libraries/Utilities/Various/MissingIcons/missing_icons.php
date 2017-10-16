<?php
namespace Chamilo\Libraries\Utilities\Various\MissingIcons;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;

require_once realpath(__DIR__ . '/../../../../../') . '/vendor/autoload.php';

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get('chamilo.libraries.architecture.bootstrap.bootstrap')->setup();

$sizes = array(Theme::ICON_MINI, Theme::ICON_SMALL, Theme::ICON_MEDIUM, Theme::ICON_BIG);

$total_failures = 0;
$total_packages = 0;
$total_missing_icons = 0;

$page = Page::getInstance();
$page->setViewMode(Page::VIEW_MODE_HEADERLESS);

$html = array();

$html[] = $page->getHeader()->toHtml();

$package_list = \Chamilo\Configuration\Package\PlatformPackageBundles::getInstance()->get_type_packages();

$source = Request::get('source');
$target = Theme::getInstance()->getTheme();

foreach ($package_list as $category => $packages)
{
    $total_packages += count($packages);
    $failures = 0;
    $data = array();

    foreach ($packages as $package)
    {
        $image_web_path = Path::getInstance()->namespaceToFullPath($package, true) . '/Resources/Images/';
        $image_sys_path = Path::getInstance()->namespaceToFullPath($package) . '/Resources/Images/';

        $data_row = array();
        $data_row[] = ClassnameUtilities::getInstance()->getPackageNameFromNamespace($package);

        $row_failures = 0;

        foreach ($sizes as $size)
        {
            // Regular
            $size_icon_path = $image_sys_path . $target . '/Logo/' . $size . '.png';

            if ($source && $source != $target)
            {
                $source_size_icon_path = $image_sys_path . $source . '/Logo/' . $size . '.png';

                if (file_exists($source_size_icon_path))
                {
                    Filesystem::copy_file($source_size_icon_path, $size_icon_path, true);
                }
            }

            if (! file_exists($size_icon_path))
            {
                $row_failures ++;
                $failures ++;
                $data_row[] = '<img src="' . Theme::getInstance()->getCommonImagePath('Error/' . $size) . '" />';
            }
            else
            {
                $data_row[] = '<img src="' . $image_web_path . $target . '/Logo/' . $size . '.png" />';
            }

            // Not available
            $icon_path = $image_sys_path . $target . '/Logo/' . $size . '_na.png';

            if ($source && $source != $target)
            {
                $source_icon_path = $image_sys_path . $source . '/Logo/' . $size . '_na.png';

                if (file_exists($source_icon_path))
                {
                    Filesystem::copy_file($source_size_icon_path, $size_icon_path, true);
                }
            }

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
                $data_row[] = '<img src="' . $image_web_path . $target . '/Logo/' . $size . '_na.png" />';
            }

            // New
            $icon_path = $image_sys_path . $target . '/Logo/' . $size . '_new.png';

            if ($source && $source != $target)
            {
                $source_icon_path = $image_sys_path . $source . '/Logo/' . $size . '_new.png';

                if (file_exists($source_icon_path))
                {
                    Filesystem::copy_file($source_size_icon_path, $size_icon_path, true);
                }
            }

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
                $data_row[] = '<img src="' . $image_web_path . $target . '/Logo/' . $size . '_new.png" />';
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

    if ($failures || Request::get('show_all'))
    {
        $html[] = '<h3>' . $category . ' ( ' . count($packages) . ' packages)</h3>';
        $html[] = $table->toHtml();
        $html[] = '<b>Missing icons: ' . $failures . '</b>';

        $total_failures += $failures;
        $total_missing_icons += $failures;
    }
}

if (! $total_failures && ! Request::get('show_all'))
{
    $html[] = Display::message(
        Display::MESSAGE_TYPE_CONFIRM,
        'Th-th-th-th-That\'s all, folks! All icons for the collected ' . $total_packages . ' packages are available!',
        true);
}
else
{
    $html[] = '<h3>Total number of packages: ' . $total_packages . '</h3>';
    $html[] = '<h3>Total number of missing icons: ' . $total_missing_icons . '</h3>';
}

$html[] = '<style>';
$html[] = 'table td {text-align: center;}';
$html[] = 'table td:first-child {text-align: left;}';
$html[] = 'table th {width: 70px; text-align: center !important;}';
$html[] = 'table th:first-child {width: auto; text-align: left !important;}';
$html[] = '</style>';
$html[] = $page->getFooter()->toHtml();

echo implode(PHP_EOL, $html);