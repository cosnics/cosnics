<?php
namespace Chamilo\Core\Repository\Common\Includes;

use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.lib
 */
abstract class ContentObjectIncludeParser
{

    /**
     * The form
     */
    private $form;

    public function __construct($form)
    {
        $this->form = $form;
    }

    public function get_form()
    {
        return $this->form;
    }

    public function set_form($form)
    {
        $this->form = $form;
    }

    abstract public function parse_editor();

    public static function factory($type, $form)
    {
        $class = __NAMESPACE__ . '\Type\Include' . StringUtilities::getInstance()->createString($type)->upperCamelize() .
             'Parser';
        return new $class($form);
    }

    public static function get_include_types()
    {
        return array('image', 'embed', 'youtube', 'chamilo');
    }

    public static function parse_includes($form)
    {
        $content_object = $form->get_content_object();

        $form_type = $form->get_form_type();

        if ($form_type == ContentObjectForm::TYPE_EDIT)
        {
            /*
             * TODO: Make this faster by providing a function that matches the existing IDs against the ones that need
             * to be added, and attaches and detaches accordingly.
             */
            foreach ($content_object->get_includes() as $included_object)
            {
                $content_object->exclude_content_object($included_object->get_id());
            }
        }

        $include_types = self::get_include_types();
        foreach ($include_types as $include_type)
        {
            $parser = self::factory($include_type, $form);
            $parser->parse_editor();
        }
    }
}
