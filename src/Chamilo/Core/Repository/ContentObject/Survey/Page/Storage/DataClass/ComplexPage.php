<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Survey\Display\Interfaces\SurveyDisplayItem;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;

/**
 *
 * @package repository.content_object.survey_page
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class ComplexPage extends ComplexContentObjectItem implements SurveyDisplayItem
{
    const PROPERTY_VISIBLE = 'visible';

    static function get_additional_property_names()
    {
        return array(self::PROPERTY_VISIBLE);
    }

    function get_visible()
    {
        return $this->get_additional_property(self::PROPERTY_VISIBLE);
    }

    function set_visible($value)
    {
        $this->set_additional_property(self::PROPERTY_VISIBLE, $value);
    }

    function is_visible()
    {
        return $this->get_visible() == 1;
    }

    function toggle_visibility()
    {
        $this->set_visible(! $this->get_visible());
    }

    function getDataAttributes()
    {
        return null;
    }
}
?>