<?php
namespace Chamilo\Core\Repository\Processor\Ckeditor;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Processor\HtmlEditorProcessor;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Exception;

class Processor extends HtmlEditorProcessor
{

    /**
     *
     * @return string
     */
    public function run()
    {
        $selected_object = $this->get_selected_content_objects();

        if (is_array($selected_object) && count($selected_object) > 0)
        {
            $selected_object = $selected_object[0];
        }

        try
        {
            /**
             *
             * @var ContentObject $object
             */
            $object = DataManager::retrieve_by_id(
                ContentObject::class_name(), $selected_object
            );

            $display = ContentObjectRenditionImplementation::factory($object, 'json', 'image', $this);

            if ($object instanceof File)
            {
                if ($object->is_image())
                {
                    $type = 'image';
                }
                else
                {
                    $type = 'file';
                }
            }
            else
            {
                $type = ClassnameUtilities::getInstance()->getClassNameFromNamespace($object->get_type(), true);
            }

            $rendition = $display->render();
        }
        catch (Exception $ex)
        {
            $rendition = array('url' => null);
        }

        $html = array();
        $html[] = '<script type="text/javascript">';
        $html[] = 'window.opener.CKEDITOR.tools.callFunction(' . $this->get_parameter('CKEditorFuncNum') . ', "' .
            $rendition['url'] . '"' . ', ' . $object->getId() . ', "' . $object->calculate_security_code() . '"' .
            ', "' . $type . '"' . ');';
        // . '\');';
        $html[] = 'window.close();';

        $html[] = '</script>';

        return implode(PHP_EOL, $html);
    }
}
