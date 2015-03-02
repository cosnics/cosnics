<?php
use libraries\HttpHeader;
use libraries\CssUtilities;
use libraries\file\Path;
use libraries\platform\session\Request;
use Chamilo\Libraries\Utilities\Utilities;

require_once __DIR__ . '/../../../../../Libraries/Architecture/Bootstrap.php';
\libraries\architecture\Bootstrap :: getInstance()->setup();

HttpHeader :: content_type(HttpHeader :: CONTENT_TYPE_CSS, CssUtilities :: DEFAULT_CHARSET);

$theme = Request :: get('theme'); // theme in the url means that changing the theme will invalidate the cache

$content_object_types = \core\repository\storage\DataManager :: get_registered_types();

foreach ($content_object_types as $content_object_type)
{
    $path = Path :: getInstance()->namespaceToFullPath('Chamilo\Core\Repository\ContentObject') . DIRECTORY_SEPARATOR .
         ClassnameUtilities :: getInstance()->getClassNameFromNamespace($content_object_type) . 'Resources/Css/' .
         (string) StringUtilities :: getInstance()->createString($theme)->upperCamelize() . '/HtmlEditor/Ckeditor/' .
         $theme . '.css';
    echo CssUtilities :: get($path);
}