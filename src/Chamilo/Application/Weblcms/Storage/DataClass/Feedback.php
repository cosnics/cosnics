<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

class Feedback extends \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback
{
    const PROPERTY_PUBLICATION_ID = 'publication_id';

    /**
     * Get the default properties of all feedback
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = [])
    {
        return parent::get_default_property_names(array(self::PROPERTY_PUBLICATION_ID));
    }

    public function get_publication_id()
    {
        return $this->get_default_property(self::PROPERTY_PUBLICATION_ID);
    }

    public function set_publication_id($publication_id)
    {
        $this->set_default_property(self::PROPERTY_PUBLICATION_ID, $publication_id);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'weblcms_feedback';
    }
}