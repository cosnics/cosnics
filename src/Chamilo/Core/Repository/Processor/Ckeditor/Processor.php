<?php
namespace Chamilo\Core\Repository\Processor\Ckeditor;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Exception;

class Processor
{
    private $parent;

    private $selected_content_objects;

    public function __construct($parent, $selected_content_objects)
    {
        $this->set_parent($parent);
        $this->set_selected_content_objects($selected_content_objects);
    }

    /**
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
             * @var ContentObject $object
             */
            $object = DataManager::retrieve_by_id(
                ContentObject::class, $selected_object
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
                $type = ClassnameUtilities::getInstance()->getClassNameFromNamespace($object->getType(), true);
            }

            $rendition = $display->render();
        }
        catch (Exception $ex)
        {
            $rendition = ['url' => null];
        }

        $html = [];
        $html[] = '<script>';
        $html[] = 'window.opener.CKEDITOR.tools.callFunction(' . $this->get_parameter('CKEditorFuncNum') . ', "' .
            $rendition['url'] . '"' . ', ' . $object->getId() . ', "' . $object->calculate_security_code() . '"' .
            ', "' . $type . '"' . ');';
        // . '\');';
        $html[] = 'window.close();';

        $html[] = '</script>';

        return implode(PHP_EOL, $html);
    }

    public function get_parent()
    {
        return $this->parent;
    }

    public static function get_repository_document_display_matching_url()
    {
        $matching_url = self::get_repository_document_display_url(
            [Manager::PARAM_CONTENT_OBJECT_ID => '', ContentObject::PARAM_SECURITY_CODE => '']
        );
        $matching_url = preg_quote($matching_url);

        $original_object_string = '&' . Manager::PARAM_CONTENT_OBJECT_ID . '\=';
        $replace_object_string = '&' . Manager::PARAM_CONTENT_OBJECT_ID . '\=[0-9]+';

        $matching_url = str_replace($original_object_string, $replace_object_string, $matching_url);

        $original_object_string = '&' . ContentObject::PARAM_SECURITY_CODE . '\=';
        $replace_object_string = '(&' . ContentObject::PARAM_SECURITY_CODE . '\=[^\&]+)?';

        $matching_url = str_replace($original_object_string, $replace_object_string, $matching_url);

        return '/' . $matching_url . '/';
    }

    public static function get_repository_document_display_url($parameters = [], $filter = [], $encode_entities = false)
    {
        $parameters = array_merge(
            [Manager::PARAM_ACTION => Manager::ACTION_DOWNLOAD_DOCUMENT, 'display' => 1], $parameters
        );

        /**
         * @var \Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator $urlGenerator
         */
        $urlGenerator = DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(UrlGenerator::class);

        return $urlGenerator->fromParameters($parameters, $filter);
    }

    public function get_selected_content_objects()
    {
        return $this->selected_content_objects;
    }

    public function set_parent($parent)
    {
        $this->parent = $parent;
    }

    public function set_selected_content_objects($selected_content_objects)
    {
        $this->selected_content_objects = $selected_content_objects;
    }
}
