<?php
namespace Chamilo\Configuration\Repository;

use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Configuration\Storage\DataClass\Language;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Libraries\Storage\DataManager\Doctrine\DataSourceName;
use Doctrine\DBAL\DriverManager;

/**
 *
 * @package Chamilo\Configuration\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ConfigurationRepository
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    private $dataClassRepository;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected function getDataClassRepository()
    {
        return $this->dataClassRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    protected function setDataClassRepository($dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @return boolean
     */
    public function isAvailable($driver, $userName, $host, $name, $password)
    {
        $configuration = new \Doctrine\DBAL\Configuration();
        $dataSourceName = new DataSourceName($driver, $userName, $host, $name, $password);

        $connectionParameters = array(
            'user' => $dataSourceName->get_username(),
            'password' => $dataSourceName->get_password(),
            'host' => $dataSourceName->get_host(),
            'driverClass' => $dataSourceName->get_driver(true));

        try
        {
            DriverManager::getConnection($connectionParameters, $configuration)->connect();
            return true;
        }
        catch (\Exception $exception)
        {
            return false;
        }
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findRegistrations()
    {
        return $this->getDataClassRepository()->records(Registration::class_name(), new RecordRetrievesParameters());
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findSettings()
    {
        return $this->getDataClassRepository()->records(Setting::class_name(), new RecordRetrievesParameters());
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findLanguages()
    {
        return $this->getDataClassRepository()->records(Language::class_name(), new RecordRetrievesParameters());
    }
}