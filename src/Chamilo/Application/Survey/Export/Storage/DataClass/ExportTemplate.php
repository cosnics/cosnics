<?php
namespace Chamilo\Application\Survey\Export\Storage\DataClass;

use Chamilo\Application\Survey\Export\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

class ExportTemplate extends DataClass
{
    const TABLE_NAME = 'export_template';
    const PROPERTY_ID = 'id';
    const PROPERTY_OWNER_ID = 'owner_id';
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_EXPORT_REGISTRATION_ID = 'export_registration_id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';

    /**
     * Get the default properties
     * 
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(
            self::PROPERTY_PUBLICATION_ID, 
            self::PROPERTY_EXPORT_REGISTRATION_ID, 
            self::PROPERTY_OWNER_ID, 
            self::PROPERTY_ID, 
            self::PROPERTY_NAME, 
            self::PROPERTY_DESCRIPTION);
    }

    function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Returns the publication_id of this ExportTemplate.
     * 
     * @return the publication_id.
     */
    function get_publication_id()
    {
        return $this->get_default_property(self::PROPERTY_PUBLICATION_ID);
    }

    /**
     * Sets the publication_id of this ExportTemplate.
     * 
     * @param publication_id
     */
    function set_publication_id($publication_id)
    {
        $this->set_default_property(self::PROPERTY_PUBLICATION_ID, $publication_id);
    }

    /**
     * Returns the export_registration_id of this ExportTemplate.
     * 
     * @return the export_registration_id.
     */
    function get_export_registration_id()
    {
        return $this->get_default_property(self::PROPERTY_EXPORT_REGISTRATION_ID);
    }

    /**
     * Sets the export_registration_id of this ExportTemplate.
     * 
     * @param export_registration_id
     */
    function set_export_registration_id($export_registration_id)
    {
        $this->set_default_property(self::PROPERTY_EXPORT_REGISTRATION_ID, $export_registration_id);
    }

    /**
     * Sets the owner of this ExportTemplate.
     * 
     * @param owner
     */
    function set_owner_id($owner)
    {
        $this->set_default_property(self::PROPERTY_OWNER_ID, $owner);
    }

    /**
     * Returns the owner of this ExportTemplate.
     * 
     * @return owner.
     */
    function get_owner_id()
    {
        return $this->get_default_property(self::PROPERTY_OWNER_ID);
    }

    /**
     * Sets the name of this ExportTemplate.
     * 
     * @param name
     */
    function set_name($name)
    {
        $this->set_default_property(self::PROPERTY_NAME, $name);
    }

    /**
     * Returns the name of this ExportTemplate.
     * 
     * @return name.
     */
    function get_name()
    {
        return $this->get_default_property(self::PROPERTY_NAME);
    }

    /**
     * Sets the description of this ExportTemplate.
     * 
     * @param description
     */
    function set_description($description)
    {
        $this->set_default_property(self::PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Returns the description of this ExportTemplate.
     * 
     * @return description.
     */
    function get_description()
    {
        return $this->get_default_property(self::PROPERTY_DESCRIPTION);
    }

    function get_type()
    {
        $export_registration = DataManager::retrieve_export_registration_by_id($this->get_export_registration_id());
        return $export_registration->get_type();
    }
}

?>