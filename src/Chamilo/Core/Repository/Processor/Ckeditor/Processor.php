<?php
namespace Chamilo\Core\Repository\Processor\Ckeditor;

use Chamilo\Core\Repository\Processor\HtmlEditorProcessor;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Session\Request;

class Processor extends HtmlEditorProcessor
{

    public function run()
    {
        $selected_object = $this->get_selected_content_objects();
        $object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object($selected_object);

        $editor = Request :: get('CKEditor');

        $html = array();
        $html[] = '<script type="text/javascript">';
        $html[] = 'window.opener.CKEDITOR.tools.callFunction(
                        ' . $this->get_parameter('CKEditorFuncNum') . ',
                        \'' .
             Path :: getInstance()->namespaceToFullPath(
                ClassnameUtilities :: getInstance()->getNamespaceFromObject($object),
                true) . 'resources/javascript/html_editor/ckeditor/dialog.js' . '\', \'' . $object->get_id() . '\', \'' .
             ClassnameUtilities :: getInstance()->getClassNameFromNamespace($object->get_type(), true) . '\');';
        $html[] = 'window.close();';

        $html[] = '</script>';

        return implode("\n", $html);
    }
}
