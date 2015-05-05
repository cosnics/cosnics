<?php
namespace Chamilo\Core\Repository\Processor\Ckeditor;

use Chamilo\Core\Repository\Processor\HtmlEditorProcessor;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

class Processor extends HtmlEditorProcessor
{

    public function run()
    {
        $selected_object = $this->get_selected_content_objects();
        $object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
            ContentObject :: class_name(),
            $selected_object);

        $editor = Request :: get('CKEditor');

        $html = array();
        $html[] = '<script type="text/javascript">';
        $html[] = 'window.opener.CKEDITOR.tools.callFunction(
                        ' . $this->get_parameter('CKEditorFuncNum') . ',
                        \'' . Path :: getInstance()->getJavascriptPath(
            ClassnameUtilities :: getInstance()->getNamespaceParent(
                ClassnameUtilities :: getInstance()->getNamespaceFromObject($object),
                2),
            true) . 'HtmlEditor/Ckeditor/dialog.js' . '\', \'' . $object->get_id() . '\', \'' .
             ClassnameUtilities :: getInstance()->getClassNameFromNamespace($object->get_type(), true) . '\');';
        $html[] = 'window.close();';

        $html[] = '</script>';

        return implode(PHP_EOL, $html);
    }
}
