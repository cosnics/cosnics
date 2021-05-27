<?php
namespace Chamilo\Core\Repository\External;

use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\PropertiesTable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

abstract class ExternalObjectDisplay
{

    /**
     *
     * @var ExternalObject
     */
    private $object;

    /**
     *
     * @param $object ExternalObject
     */
    public function __construct($object)
    {
        $this->object = $object;
    }

    /**
     *
     * @return string
     */
    public function as_html()
    {
        $html = [];
        $html[] = $this->get_title();
        $html[] = $this->get_preview() . '<br/>';
        $html[] = $this->get_properties_table();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param $object ExternalObject
     *
     * @return ExternalObjectDisplay
     */
    public static function factory($object)
    {
        $class = $object::context() . '\ExternalObjectDisplay';

        return new $class($object);
    }

    /**
     *
     * @return array
     */
    public function get_display_properties()
    {
        $object = $this->get_object();

        $properties = [];
        $properties[Translation::get('Title')] = $object->get_title();

        if ($object->get_description())
        {
            $properties[Translation::get('Description', null, Utilities::COMMON_LIBRARIES)] =
                $object->get_description();
        }

        if ($object->get_created() > 0)
        {
            $properties[Translation::get('UploadedOn')] = DatetimeUtilities::format_locale_date(
                null, $object->get_created()
            );
        }

        if ($object->get_created() != $object->get_modified())
        {
            $properties[Translation::get('ModifiedOn')] = DatetimeUtilities::format_locale_date(
                null, $object->get_modified()
            );
        }

        $properties[Translation::get('OwnerName')] = $object->get_owner_name();

        return $properties;
    }

    /**
     *
     * @return ExternalObject
     */
    public function get_object()
    {
        return $this->object;
    }

    /**
     *
     * @param $is_thumbnail boolean
     *
     * @return string
     */
    public function get_preview($is_thumbnail = false)
    {
        $glyph = new FontAwesomeGlyph('image', array('fa-5x'), null, 'fas');

        if ($is_thumbnail)
        {
            $class = 'no_thumbnail';
            $text = '<h3>' . Translation::get('NoThumbnailAvailable') . '</h3>';
        }
        else
        {
            $class = 'no_preview';
            $text = '<h1>' . Translation::get('NoPreviewAvailable') . '</h1>';
        }

        $html = [];
        $html[] = '<div class="' . $class . '">';
        $html[] = '<div class="background">';
        $html[] = $glyph->render();
        $html[] = $text;
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function get_properties_table()
    {
        $object = $this->get_object();

        $properties = $this->get_display_properties();
        if (count($properties) > 0)
        {
            $table = new PropertiesTable($properties);
            $table->setAttribute('style', 'margin-top: 1em; margin-bottom: 0;');

            return $table->toHtml();
        }
    }

    /**
     *
     * @return string
     */
    public function get_title()
    {
        $object = $this->get_object();

        return '<h3>' . $object->get_title() . '</h3>';
    }
}
