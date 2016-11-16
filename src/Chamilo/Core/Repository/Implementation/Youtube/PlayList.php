<?php
namespace Chamilo\Core\Repository\Implementation\Youtube;

use Chamilo\Libraries\Storage\DataClass\DataClass;

class PlayList extends DataClass
{
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DATE = 'date';
    const PROPERTY_DESCRIPTION = 'description';

    public function get_title()
    {
        return $this->get_default_property(self::PROPERTY_TITLE);
    }

    public function set_title($title)
    {
        return $this->set_default_property(self::PROPERTY_TITLE, $title);
    }

    public function get_description()
    {
        return $this->get_default_property(self::PROPERTY_DESCRIPTION);
    }

    public function set_description($description)
    {
        return $this->set_default_property(self::PROPERTY_DESCRIPTION, $description);
    }

    public function get_date()
    {
        return $this->get_default_property(self::PROPERTY_DATE);
    }

    public function set_date($date)
    {
        return $this->set_default_property(self::PROPERTY_DATE, $date);
    }

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(self::PROPERTY_DATE, self::PROPERTY_TITLE, self::PROPERTY_DESCRIPTION));
    }
}
