<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\Factory;

use Chamilo\Libraries\Storage\DataManager\AdoDb\DataSourceName;
use Chamilo\Libraries\Storage\Exception\ConnectionException;
use Exception;

require_once realpath(__DIR__ . '/../../../../../../../') . '/vendor/adodb/adodb-php/adodb.inc.php';

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConnectionFactory
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\AdoDb\DataSourceName
     */
    private $dataSourceName;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\AdoDb\DataSourceName $dataSourceName
     */
    public function __construct(DataSourceName $dataSourceName)
    {
        $this->dataSourceName = $dataSourceName;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\DataSourceName
     */
    public function getDataSourceName()
    {
        return $this->dataSourceName;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\AdoDb\DataSourceName $dataSourceName
     */
    public function setDataSourceName($dataSourceName)
    {
        $this->dataSourceName = $dataSourceName;
    }

    /**
     *
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     * @return \ADOConnection
     */
    public function getConnection()
    {
        $dataSourceName = $this->getDataSourceName();
        $connection = $this->getAdoConnection();

        $connection->pConnect(
            $dataSourceName->getConnectionString(),
            $dataSourceName->get_username(),
            $dataSourceName->get_password());

        $connection->SetFetchMode(ADODB_FETCH_ASSOC);

        try
        {
            return $connection;
        }
        catch (Exception $exception)
        {
            throw new ConnectionException('Could not connect to the database. Please contact your system administrator.');
        }
    }

    /**
     *
     * @return \ADOConnection
     */
    protected function getAdoConnection()
    {
        return adoNewConnection('pdo');
    }
}
