<?php
namespace Chamilo\Core\Install\Storage;

use Chamilo\Core\Install\Configuration;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\DataManager\DataSourceName;
use Doctrine\Common\ClassLoader;
use Doctrine\DBAL\DriverManager;

class DataManager
{

    /**
     *
     * @var \Doctrine\DBAL\Connection
     */
    private $connection = null;

    /**
     *
     * @var boolean did the databse exist before install ?
     */
    private $database_exists = false;

    /**
     *
     * @var Configuration $installer_config
     */
    private $installer_config = null;

    public function __construct(Configuration $installer_config)
    {
        $this->installer_config = $installer_config;
    }

    public function set_installer_config(Configuration $installer_config)
    {
        $this->installer_config = $installer_config;
    }

    public function set_connection($connection)
    {
        $this->connection = $connection;
    }

    public function init_storage_access()
    {
        $classLoader = new ClassLoader('Doctrine', Path :: getInstance()->getPluginPath());
        $classLoader->register();
        
        $data_source_name = DataSourceName :: factory(
            'Doctrine',
            $this->installer_config->get_db_driver(), 
            $this->installer_config->get_db_username(), 
            $this->installer_config->get_db_host(), 
            $this->installer_config->get_db_name(), 
            $this->installer_config->get_db_password());
        
        $configuration = new \Doctrine\DBAL\Configuration();
        $connection_parameters = array(
            'dbname' => $data_source_name->get_database(), 
            'user' => $data_source_name->get_username(), 
            'password' => $data_source_name->get_password(), 
            'host' => $data_source_name->get_host(), 
            'driverClass' => $data_source_name->get_driver(true));
        
        $connection = DriverManager :: getConnection($connection_parameters, $configuration);
        
        try
        {
            $connection->connect();
            $this->database_exists = true;
        }
        catch (\Exception $exception)
        {
            $this->database_exists = false;
        }
    }

    public function init_storage_structure()
    {
        $name = $this->installer_config->get_db_name();
        $overwrite = $this->installer_config->get_db_overwrite();
        
        $data_source_name = DataSourceName :: factory(
            'Doctrine',
            $this->installer_config->get_db_driver(), 
            $this->installer_config->get_db_username(), 
            $this->installer_config->get_db_host(), 
            null, 
            $this->installer_config->get_db_password());
        
        $configuration = new \Doctrine\DBAL\Configuration();
        $connection_parameters = array(
            'dbname' => $data_source_name->get_database(), 
            'user' => $data_source_name->get_username(), 
            'password' => $data_source_name->get_password(), 
            'host' => $data_source_name->get_host(), 
            'driverClass' => $data_source_name->get_driver(true));
        
        $connection = DriverManager :: getConnection($connection_parameters, $configuration);
        
        if (! $this->database_exists)
        {
            $connection->getSchemaManager()->createDatabase($name);
        }
        
        elseif ($this->database_exists && $overwrite)
        {
            $connection->getSchemaManager()->dropAndCreateDatabase($name);
        }
        $this->database_exists = true;
    }
}