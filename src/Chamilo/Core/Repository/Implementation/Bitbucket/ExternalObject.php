<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket;

/**
 *
 * @author magali.gillard
 */
class ExternalObject extends \Chamilo\Core\Repository\External\ExternalObject
{
    const OBJECT_TYPE = 'bitbucket';
    const PROPERTY_LOGO = 'logo';
    const PROPERTY_WEBSITE = 'website';

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(array(self::PROPERTY_LOGO));
    }

    public static function get_object_type()
    {
        return self::OBJECT_TYPE;
    }

    public function get_logo()
    {
        return $this->get_default_property(self::PROPERTY_LOGO);
    }

    public function set_logo($logo)
    {
        return $this->set_default_property(self::PROPERTY_LOGO, $logo);
    }

    public function get_website()
    {
        return $this->get_default_property(self::PROPERTY_WEBSITE);
    }

    public function set_website($website)
    {
        return $this->set_default_property(self::PROPERTY_WEBSITE, $website);
    }

    public function get_tags()
    {
        return $this->get_connector()->retrieve_tags($this->get_id());
    }

    public function get_branches()
    {
        return $this->get_connector()->retrieve_branches($this->get_id());
    }

    public function get_changesets($limit = 5)
    {
        return $this->get_connector()->retrieve_changesets($this->get_id(), $limit);
    }

    public function get_most_recent_changeset()
    {
        return array_pop($this->get_changesets(1));
    }

    public function get_privileges()
    {
        return $this->get_connector()->retrieve_privileges($this->get_id());
    }

    public function get_groups_privileges()
    {
        return $this->get_connector()->retrieve_groups_privileges($this->get_id());
    }

    public function get_download_link()
    {
        if ($this->get_most_recent_changeset())
        {
            return $this->get_most_recent_changeset()->get_download_link();
        }
        else
        
        {
            return false;
        }
    }

    public function get_slug()
    {
        $test = explode('/', $this->get_id());
        return $test[1];
    }
}
