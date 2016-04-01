<?php
namespace Chamilo\Configuration\Package\Action;

use Chamilo\Configuration\Package\Action;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use DOMDocument;

/**
 *
 * @author Hans De Bisschop
 * @author Magali Gillard
 * @author Sven Vanpoucke - Hogeschool Gent - Refinement of generic methods
 */
abstract class Upgrader extends Action
{
    const INDEX_TYPE_PRIMARY = 1;
    const INDEX_TYPE_NORMAL = 2;
    const INDEX_TYPE_UNIQUE = 3;

    /**
     *
     * @var \libraries\storage\data_manager\DataManager
     */
    private $data_manager;

    /**
     *
     * @param \libraries\storage\data_manager\DataManager $data_manager
     */
    public function __construct($data_manager = null)
    {
        $this->data_manager = $data_manager;
    }

    /**
     *
     * @param string $context
     * @param \libraries\storage\data_manager\DataManager $data_manager
     *
     * @return \configuration\package\Upgrader
     */
    public static function factory($context)
    {
        $package = Package :: get($context);
        $version = str_replace(array('-', '+', '.'), '_', $package->get_version());

        $class = $context . '\Package\Upgrade\V' . $version . '\Upgrader';

        if (! class_exists($class))
        {
            throw new \Exception(Translation :: get('UpgraderNotFound', array('CONTEXT' => $context)));
        }

        return new $class();
    }

    /**
     * Upgrades the package
     *
     * @return boolean
     */
    abstract function run();

    /**
     *
     * @param \libraries\storage\data_manager\DataManager $data_manager
     */
    public function set_data_manager($data_manager)
    {
        $this->data_manager = $data_manager;
    }

    /**
     *
     * @return \libraries\storage\data_manager\DataManager
     */
    public function get_data_manager()
    {
        return $this->data_manager;
    }

    /**
     * Deduces the package name by the giving context of the called class
     *
     * @return string
     */
    protected function get_package_name()
    {
        $class = get_class($this);

        return ClassnameUtilities :: getInstance()->getNamespaceParent(
            ClassnameUtilities :: getInstance()->getNamespaceParent(
                ClassnameUtilities :: getInstance()->getNamespaceFromClassname($class)));
    }

    /**
     * Parses an XML file describing a storage unit.
     * For defining the 'type' of the field, the same definition is used
     * as the PEAR::MDB2 package. See http://pear.php.net/manual/en/package.database. mdb2.datatypes.php
     *
     * @param $file string The complete path to the XML-file from which the storage unit definition should be read.
     * @return array An with values for the keys 'name','properties' and 'indexes'
     */
    public static function parse_xml_file($file)
    {
        $name = '';
        $properties = array();
        $indexes = array();

        $doc = new DOMDocument();
        $doc->load($file);
        $object = $doc->getElementsByTagname('object')->item(0);
        $name = $object->getAttribute('name');
        $xml_properties = $doc->getElementsByTagname('property');
        $attributes = array('type', 'length', 'unsigned', 'notnull', 'default', 'autoincrement', 'fixed');
        foreach ($xml_properties as $index => $property)
        {
            $property_info = array();
            foreach ($attributes as $index => $attribute)
            {
                if ($property->hasAttribute($attribute))
                {
                    $property_info[$attribute] = $property->getAttribute($attribute);
                }
            }
            $properties[$property->getAttribute('name')] = $property_info;
        }
        $xml_indexes = $doc->getElementsByTagname('index');
        foreach ($xml_indexes as $key => $index)
        {
            $index_info = array();
            $index_info['type'] = $index->getAttribute('type');
            $index_properties = $index->getElementsByTagname('indexproperty');
            foreach ($index_properties as $subkey => $index_property)
            {
                $index_info['fields'][$index_property->getAttribute('name')] = array(
                    'length' => $index_property->getAttribute('length'));
            }
            $indexes[$index->getAttribute('name')] = $index_info;
        }
        $result = array();
        $result['name'] = $name;
        $result['properties'] = $properties;
        $result['indexes'] = $indexes;

        return $result;
    }

    /**
     * Returns the list with extra upgradable packages that are connected to this package
     *
     * @return multitype:string
     */
    public function get_additional_packages()
    {
        return array();
    }

    /**
     * Common functionality used in upgrade scripts
     */

    /**
     * Adds a registration for a new package
     *
     * @param $package
     * @return bool
     */
    protected function add_package_registration($package)
    {
        $package_info = Package :: get($package);
        $application_registration = new \Chamilo\Configuration\Storage\DataClass\Registration();

        $parent_namespace = ClassnameUtilities :: getInstance()->getNamespaceParent($package);
        $application_registration->set_type((! $parent_namespace ? 'core' : $parent_namespace));

        $application_registration->set_name($package_info->get_code());
        $application_registration->set_category($package_info->get_category());
        $application_registration->set_version($package_info->get_version());
        $application_registration->set_status(\Chamilo\Configuration\Storage\DataClass\Registration :: STATUS_ACTIVE);
        $application_registration->set_context($package);

        if (! $application_registration->create())
        {
            $this->add_message(
                Upgrader :: TYPE_ERROR,
                Translation :: get(
                    'PackageRegistrationCreationFailed',
                    array('PACKAGE' => $package),
                    Utilities :: COMMON_LIBRARIES));

            return false;
        }

        $this->add_message(
            Upgrader :: TYPE_NORMAL,
            Translation :: get(
                'PackageRegistrationCreated',
                array('PACKAGE' => $package),
                Utilities :: COMMON_LIBRARIES));

        return true;
    }

    /**
     * Upgrades the registration for a given package
     *
     * @param string $package
     *
     * @return bool
     */
    protected function upgrade_package_registration($package)
    {
        $package_info = Package :: get($package);

        $registration = \Chamilo\Configuration\Storage\DataManager :: retrieveRegistrationByContext(self :: context());
        $registration->set_version($package_info->get_version());

        if (! $registration->update())
        {
            $this->add_message(
                Upgrader :: TYPE_ERROR,
                Translation :: get(
                    'UpgradeRegistrationFailed',
                    array('PACKAGE' => $package),
                    Utilities :: COMMON_LIBRARIES));

            return false;
        }

        $this->add_message(
            Upgrader :: TYPE_NORMAL,
            Translation :: get(
                'UpgradeRegistrationSuccessful',
                array('PACKAGE' => $package),
                Utilities :: COMMON_LIBRARIES));

        return true;
    }

    /**
     * Creates a storage unit in the database
     *
     * @param string $storage_unit - The table name of the storage unit
     * @return bool
     */
    protected function create_storage_unit($storage_unit)
    {
        $data_manager = $this->get_data_manager();

        $path = Path :: getInstance()->namespaceToFullPath($data_manager :: context()) . 'php/package/install/' .
             $storage_unit . '.xml';

        if (! file_exists($path))
        {
            $this->add_message(
                Upgrader :: TYPE_ERROR,
                Translation :: get('NoStorageUnitFound', array('UNIT' => $storage_unit), Utilities :: COMMON_LIBRARIES));

            return false;
        }

        $storage_unit_info = self :: parse_xml_file($path);

        if (! $data_manager->storage_unit_exists($storage_unit_info['name']))
        {
            if (! $data_manager->create_storage_unit(
                $storage_unit_info['name'],
                $storage_unit_info['properties'],
                $storage_unit_info['indexes']))
            {
                $this->add_message(
                    Upgrader :: TYPE_ERROR,
                    Translation :: get(
                        'CreateStorageUnitFailed',
                        array('UNIT' => $storage_unit_info['name']),
                        Utilities :: COMMON_LIBRARIES));

                return false;
            }
            else
            {
                $this->add_message(
                    Upgrader :: TYPE_NORMAL,
                    Translation :: get(
                        'CreateStorageUnitSuccessful',
                        array('UNIT' => $storage_unit_info['name']),
                        Utilities :: COMMON_LIBRARIES));
            }
        }

        return true;
    }

    /**
     * Creates new storage units based on an array of storage unit names
     *
     * @param array $storage_units
     *
     * @return bool
     */
    protected function create_storage_units(array $storage_units = array())
    {
        foreach ($storage_units as $storage_unit)
        {
            if (! $this->create_storage_unit($storage_unit))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Renames a storage_unit
     *
     * @param string $old_storage_unit
     * @param string $new_storage_unit
     *
     * @return bool
     */
    protected function rename_storage_unit($old_storage_unit, $new_storage_unit)
    {
        $data_manager = $this->get_data_manager();

        if (! $data_manager->rename_storage_unit($old_storage_unit, $new_storage_unit))
        {
            $this->add_message(
                Upgrader :: TYPE_ERROR,
                Translation :: get(
                    'TableNotRenamed',
                    array('OLD_TABLE_NAME' => $old_storage_unit, 'NEW_TABLE_NAME' => $new_storage_unit),
                    Utilities :: COMMON_LIBRARIES));

            return false;
        }

        $this->add_message(
            Upgrader :: TYPE_NORMAL,
            Translation :: get(
                'TableRenamed',
                array('OLD_TABLE_NAME' => $old_storage_unit, 'NEW_TABLE_NAME' => $new_storage_unit),
                Utilities :: COMMON_LIBRARIES));

        return true;
    }

    /**
     * Renames multiple storage units based on an array with old_storage_unit => new_storage_unit
     *
     * @param array $storage_units
     *
     * @return bool
     */
    protected function rename_storage_units(array $storage_units = array())
    {
        foreach ($storage_units as $old_storage_unit => $new_storage_unit)
        {
            if (! $this->rename_storage_unit($old_storage_unit, $new_storage_unit))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Alters the properties of a column
     *
     * @param string $table_name
     * @param string $column_name
     * @param array $attributes
     *
     * @return bool
     */
    protected function change_column($table_name, $column_name, $attributes = array())
    {
        return $this->alter_storage_unit(
            \Chamilo\Libraries\Storage\DataManager\DataManager :: ALTER_STORAGE_UNIT_CHANGE,
            $table_name,
            $column_name,
            $attributes,
            'ColumnChanged',
            'ColumnNotChanged');
    }

    /**
     * Alters the properties of multiple columns based on an array with table_name => column_name => attributes
     *
     * @param array $columns
     *
     * @return bool
     */
    protected function change_columns(array $columns)
    {
        foreach ($columns as $table_name => $table_columns)
        {
            foreach ($table_columns as $column_name => $attributes)
            {
                if (! $this->change_column($table_name, $column_name, $attributes))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Adds a new column to a database
     *
     * @param $table_name
     * @param $column_name
     * @param $attributes
     * @return bool
     */
    protected function add_column($table_name, $column_name, $attributes)
    {
        return $this->alter_storage_unit(
            \Chamilo\Libraries\Storage\DataManager\DataManager :: ALTER_STORAGE_UNIT_ADD,
            $table_name,
            $column_name,
            $attributes,
            'ColumnAdded',
            'ColumnNotAdded');
    }

    /**
     * Adds new columns based on an array with table_name => column_name => attributes
     *
     * @param array $columns
     *
     * @return bool
     */
    protected function add_columns(array $columns)
    {
        foreach ($columns as $table_name => $table_columns)
        {
            foreach ($table_columns as $column_name => $attributes)
            {
                if (! $this->add_column($table_name, $column_name, $attributes))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Deletes a column from the database
     *
     * @param $table_name
     * @param $column_name
     * @return bool
     */
    protected function delete_column($table_name, $column_name)
    {
        return $this->alter_storage_unit(
            \Chamilo\Libraries\Storage\DataManager\DataManager :: ALTER_STORAGE_UNIT_DROP,
            $table_name,
            $column_name,
            null,
            'ColumnDeleted',
            'ColumnNotDeleted');
    }

    /**
     * Deletes columns based on an array with table_name => column_names
     *
     * @param array $columns
     *
     * @return bool
     */
    protected function delete_columns(array $columns)
    {
        foreach ($columns as $table_name => $table_columns)
        {
            foreach ($table_columns as $column_name)
            {
                if (! $this->delete_column($table_name, $column_name))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Helper functionality for the add, update and delete column functions
     *
     * @param string $alter_type
     * @param string $table_name
     * @param string $column_name
     * @param array $attributes
     * @param string $success_translation_variable
     * @param string $failed_translation_variable
     *
     * @return bool
     */
    protected function alter_storage_unit($alter_type, $table_name, $column_name, array $attributes,
        $success_translation_variable, $failed_translation_variable)
    {
        $data_manager = $this->get_data_manager();
        $success = $data_manager->alter_storage_unit($alter_type, $table_name, $column_name, $attributes);

        if (! $success)
        {
            $this->add_message(
                Upgrader :: TYPE_ERROR,
                Translation :: get(
                    $failed_translation_variable,
                    array('TABLE_NAME' => $table_name, 'COLUMN_NAME' => $column_name),
                    Utilities :: COMMON_LIBRARIES));

            return false;
        }

        $this->add_message(
            Upgrader :: TYPE_NORMAL,
            Translation :: get(
                $success_translation_variable,
                array('TABLE_NAME' => $table_name, 'COLUMN_NAME' => $column_name),
                Utilities :: COMMON_LIBRARIES));

        return true;
    }

    /**
     * Adds an index for a given table and column(s)
     *
     * @param int $index_type
     * @param string $table_name
     * @param string $name
     * @param array $columns
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    protected function add_index($index_type, $table_name, $name, array $columns)
    {
        $alter_type = null;

        switch ($index_type)
        {
            case self :: INDEX_TYPE_PRIMARY :
                $alter_type = \Chamilo\Libraries\Storage\DataManager\DataManager :: ALTER_STORAGE_UNIT_ADD_PRIMARY_KEY;
                break;
            case self :: INDEX_TYPE_NORMAL :
                $alter_type = \Chamilo\Libraries\Storage\DataManager\DataManager :: ALTER_STORAGE_UNIT_ADD_INDEX;
                break;
            case self :: INDEX_TYPE_UNIQUE :
                $alter_type = \Chamilo\Libraries\Storage\DataManager\DataManager :: ALTER_STORAGE_UNIT_ADD_INDEX;
                break;
            default :
                throw new \InvalidArgumentException(
                    Translation :: get(
                        'IndexTypeInvalid',
                        array('INDEX_TYPE' => $index_type),
                        Utilities :: COMMON_LIBRARIES));
        }

        return $this->alter_storage_unit_index($alter_type, $table_name, $name, $columns, 'IndexAdded', 'IndexNotAdded');
    }

    /**
     * Adds indexes based on an array with table_name => type => name => columns
     *
     * @param array $indexes
     *
     * @return bool
     */
    protected function add_indexes(array $indexes)
    {
        foreach ($indexes as $table_name => $index_types)
        {
            foreach ($index_types as $index_type => $index)
            {
                foreach ($index as $index_name => $columns)
                {
                    if (! $this->add_index($index_type, $table_name, $index_name, $columns))
                    {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Deletes an index for a given table and column(s)
     *
     * @param int $index_type
     * @param string $table_name
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    protected function delete_index($index_type, $table_name, $name)
    {
        $alter_type = null;

        switch ($index_type)
        {
            case self :: INDEX_TYPE_PRIMARY :
                $alter_type = \Chamilo\Libraries\Storage\DataManager\DataManager :: ALTER_STORAGE_UNIT_DROP_PRIMARY_KEY;
                break;
            case self :: INDEX_TYPE_NORMAL :
                $alter_type = \Chamilo\Libraries\Storage\DataManager\DataManager :: ALTER_STORAGE_UNIT_DROP_INDEX;
                break;
            case self :: INDEX_TYPE_UNIQUE :
                $alter_type = \Chamilo\Libraries\Storage\DataManager\DataManager :: ALTER_STORAGE_UNIT_DROP_INDEX;
                break;
            default :
                throw new \InvalidArgumentException(
                    Translation :: get(
                        'IndexTypeInvalid',
                        array('INDEX_TYPE' => $index_type),
                        Utilities :: COMMON_LIBRARIES));
        }

        return $this->alter_storage_unit_index($alter_type, $table_name, $name, array(), 'IndexDeleted', 'IndexNotDeleted');
    }

    /**
     * Deletes indexes based on an array with table_name => type => name
     *
     * @param array $indexes
     *
     * @return bool
     */
    protected function delete_indexes(array $indexes)
    {
        foreach ($indexes as $table_name => $index_types)
        {
            foreach ($index_types as $index_type => $index)
            {
                foreach ($index as $index_name)
                {
                    if (! $this->delete_index($index_type, $table_name, $index_name))
                    {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Helper functionality for the add and delete of indexes
     *
     * @param string $alter_type
     * @param string $table_name
     * @param string $name
     * @param array $columns
     * @param string $success_translation_variable
     * @param string $failed_translation_variable
     *
     * @return bool
     */
    protected function alter_storage_unit_index($alter_type, $table_name, $name, array $columns,
        $success_translation_variable, $failed_translation_variable)
    {
        $data_manager = $this->get_data_manager();
        $success = $data_manager->alter_storage_unit_index($alter_type, $table_name, $name, $columns);

        if (! $success)
        {
            $this->add_message(
                Upgrader :: TYPE_ERROR,
                Translation :: get(
                    $failed_translation_variable,
                    array('TABLE_NAME' => $table_name, 'INDEX_NAME' => $name),
                    Utilities :: COMMON_LIBRARIES));

            return false;
        }

        $this->add_message(
            Upgrader :: TYPE_NORMAL,
            Translation :: get(
                $success_translation_variable,
                array('TABLE_NAME' => $table_name, 'INDEX_NAME' => $name),
                Utilities :: COMMON_LIBRARIES));

        return true;
    }

    /**
     * Deletes storage units based on an array of storage unit names
     *
     * @param array $storage_units
     *
     * @return bool
     */
    protected function delete_storage_units(array $storage_units = array())
    {
        foreach ($storage_units as $storage_unit)
        {
            if (! $this->delete_storage_unit($storage_unit))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Helper functionality to delete a storage unit
     *
     * @param string $table_name
     *
     * @return bool
     */
    protected function delete_storage_unit($table_name)
    {
        $data_manager = $this->get_data_manager();
        $success = $data_manager->drop_storage_unit($table_name);

        if (! $success)
        {
            $this->add_message(
                Upgrader :: TYPE_ERROR,
                Translation :: get(
                    'DropStorageUnitFailed',
                    array('TABLE_NAME' => $table_name),
                    Utilities :: COMMON_LIBRARIES));

            return false;
        }

        $this->add_message(
            Upgrader :: TYPE_NORMAL,
            Translation :: get(
                'DropStorageUnitSuccessful',
                array('TABLE_NAME' => $table_name),
                Utilities :: COMMON_LIBRARIES));

        return true;
    }
}
