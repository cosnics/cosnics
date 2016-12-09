<?php
namespace Chamilo\Libraries\Utilities\ThemeGenerator;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Session\Request;

require_once __DIR__ . '/../../Architecture/Bootstrap.php';
\Chamilo\Libraries\Architecture\Bootstrap :: getInstance()->setup();

function create_and_copy($base_path, $theme)
{
    $new_theme = Request :: get('theme');

    if ($new_theme)
    {
        $original_css_directory = $base_path . 'resources/css/aqua/';
        $css_directory = $base_path . 'resources/css/' . $new_theme . '/';

        if (! file_exists($css_directory) && file_exists($original_css_directory))
        {
            Filesystem :: create_dir($css_directory);
            Filesystem :: recurse_copy($original_css_directory, $css_directory);

            Filesystem :: move_file($css_directory . 'aqua.css', $css_directory . $new_theme . '.css');
        }

        $original_images_directory = $base_path . 'resources/images/aqua/';
        $images_directory = $base_path . 'resources/images/' . $new_theme . '/';

        if (! file_exists($images_directory) && file_exists($original_images_directory))
        {
            Filesystem :: create_dir($css_directory);
            Filesystem :: recurse_copy($original_images_directory, $images_directory);
        }
    }
}

/*
 * CORE APPLICATION THEME
 */
$core_applications = array(
    'core\admin',
    'core\help',
    'core\install',
    'reporting',
    'core\tracking',
    'core\repository',
    'user',
    'core\group',
    'core\rights',
    'core\home',
    'core\menu',
    'core\migration',
    'core\metadata',
    'core\context_linker');
foreach ($core_applications as $core_application)
{
    create_and_copy(Path :: getInstance()->namespaceToFullPath($core_application));
}

/*
 * OPTIONAL APPLICATIONS
 */
$optional_applications = Filesystem :: get_directory_content(
    Path :: get_application_path(),
    Filesystem :: LIST_DIRECTORIES,
    false);
foreach ($optional_applications as $optional_application)
{
    create_and_copy(Path :: getInstance()->namespaceToFullPath('application\\' . $optional_application));
}

/*
 * WEBLCMS TOOLS
 */
$weblcms_tools = Filesystem :: get_directory_content(
    Path :: getInstance()->namespaceToFullPath('application\weblcms') . 'tool/',
    Filesystem :: LIST_DIRECTORIES,
    false);
foreach ($weblcms_tools as $weblcms_tool)
{
    create_and_copy(Path :: getInstance()->namespaceToFullPath('application\weblcms') . 'tool/' . $weblcms_tool . '/');
}

/*
 * EXTENSIONS
 */
$extensions = Filesystem :: get_directory_content(
    Path :: get_common_extensions_path(),
    Filesystem :: LIST_DIRECTORIES,
    false);
foreach ($extensions as $extension)
{
    create_and_copy(Path :: get_common_extensions_path() . $extension . '/');
}

/*
 * EXTERNAL REPOSITORY MANAGERS
 */
$external_repository_managers = Filesystem :: get_directory_content(
    Path :: get_common_extensions_path() . 'external_repository_manager/implementation/',
    Filesystem :: LIST_DIRECTORIES,
    false);
foreach ($external_repository_managers as $external_repository_manager)
{
    create_and_copy(
        Path :: get_common_extensions_path() . 'external_repository_manager/implementation/' .
             $external_repository_manager . '/');
}

/*
 * VIDEO CONFERENCING MANAGERS
 */
$video_conferencing_managers = Filesystem :: get_directory_content(
    Path :: get_common_extensions_path() . 'video_conferencing_manager/implementation/',
    Filesystem :: LIST_DIRECTORIES,
    false);
foreach ($video_conferencing_managers as $video_conferencing_manager)
{
    create_and_copy(
        Path :: get_common_extensions_path() . 'video_conferencing_manager/implementation/' . $video_conferencing_manager .
             '/');
}

/*
 * CONTENT OBJECTS
 */
$content_objects = Filesystem :: get_directory_content(
    Path :: get_repository_content_object_path(),
    Filesystem :: LIST_DIRECTORIES,
    false);
foreach ($content_objects as $content_object)
{
    create_and_copy(Path :: get_repository_content_object_path() . $content_object . '/');
}

/*
 * COMMON LIBRARIES
 */
$configuration = Path :: getInstance()->getConfigurationPath();
create_and_copy($configuration);

/*
 * DISCOVERY
 */

$types = array();

$modules = Filesystem :: get_directory_content(
    Path :: getInstance()->namespaceToFullPath('application\discovery') . 'module/',
    Filesystem :: LIST_DIRECTORIES,
    false);
foreach ($modules as $module)
{
    $namespace = '\\application\discovery\module\\' . $module . '\Module';
    if (class_exists($namespace, true))
    {
        $types = array_merge($types, $namespace :: get_available_implementations());
    }
}

foreach ($types as $type)
{
    create_and_copy(Path :: getInstance()->namespaceToFullPath($type));
}
