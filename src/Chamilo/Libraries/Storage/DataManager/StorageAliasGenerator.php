<?php
namespace Chamilo\Libraries\Storage\DataManager;

use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class StorageAliasGenerator
{
    const TYPE_TABLE = 1;
    const TYPE_CONSTRAINT = 2;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator
     */
    private static $instance;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    private $classnameUtilities;

    /**
     *
     * @var string[]
     */
    private $aliases = array();

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    public function __construct(ClassnameUtilities $classnameUtilities)
    {
        $this->classnameUtilities = $classnameUtilities;
        
        foreach ($this->get_types() as $type)
        {
            $this->aliases[$type] = array();
        }
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator
     */
    public static function getInstance()
    {
        if (! isset(self::$instance))
        {
            self::$instance = new self(ClassnameUtilities::getInstance());
        }
        return self::$instance;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    public function getClassnameUtilities()
    {
        return $this->classnameUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    public function setClassnameUtilities(ClassnameUtilities $classnameUtilities)
    {
        $this->classnameUtilities = $classnameUtilities;
    }

    /**
     *
     * @return string[]
     */
    public function get_types()
    {
        return array(self::TYPE_TABLE, self::TYPE_CONSTRAINT);
    }

    /**
     *
     * @return string[]
     */
    public function get_aliases()
    {
        return $this->aliases;
    }

    /**
     *
     * @param string $class
     * @return string
     */
    public function get_data_class_alias($class)
    {
        $classnameUtilities = $this->getClassnameUtilities();
        $namespace = $classnameUtilities->getPackageNameFromNamespace(
            $classnameUtilities->getNamespaceFromClassname($class));
        return $this->get_table_alias($class::get_table_name(), $namespace . '_');
    }

    /**
     *
     * @param string $table_name
     * @return string
     */
    public function get_table_alias($table_name)
    {
        if (array_key_exists($table_name, $this->aliases[self::TYPE_TABLE]))
        {
            return $this->aliases[self::TYPE_TABLE][$table_name];
        }
        else
        {
            $possible_name = 'alias_';
            $parts = explode('_', $table_name);
            
            foreach ($parts as $part)
            {
                $possible_name .= $part{0};
            }
            
            if (in_array($possible_name, $this->aliases[self::TYPE_TABLE]))
            {
                $original_name = $possible_name;
                $index = 'a';
                
                while (in_array($possible_name, $this->aliases[self::TYPE_TABLE]))
                {
                    $possible_name = $original_name . '_' . $index;
                    $index ++;
                }
            }
            
            $this->aliases[self::TYPE_TABLE][$table_name] = $possible_name;
            
            return $possible_name;
        }
    }

    /**
     *
     * @param string $table_name
     * @param string $column
     * @return string
     */
    public function get_constraint_name($table_name, $column)
    {
        $possible_name = '';
        $parts = explode('_', $table_name);
        
        foreach ($parts as $part)
        {
            $possible_name .= $part{0};
        }
        
        $possible_name = $possible_name . '_' . $column;
        
        if (! array_key_exists($possible_name, $this->aliases[self::TYPE_CONSTRAINT]))
        {
            $this->aliases[self::TYPE_CONSTRAINT][$possible_name] = serialize(array($table_name, $column));
            return $possible_name;
        }
        else
        {
            $original_name = $possible_name;
            $index = 'a';
            
            while (array_key_exists($possible_name, $this->aliases[self::TYPE_CONSTRAINT]))
            {
                $possible_name = $original_name . '_' . $index;
                $index ++;
            }
            
            $this->aliases[self::TYPE_CONSTRAINT][$possible_name] = serialize(array($table_name, $column));
            return $possible_name;
        }
    }
}
