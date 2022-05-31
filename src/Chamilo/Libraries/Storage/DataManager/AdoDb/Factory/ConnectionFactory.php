<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\Factory;

use ADOConnection;
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

    private DataSourceName $dataSourceName;

    public function __construct(DataSourceName $dataSourceName)
    {
        $this->dataSourceName = $dataSourceName;
    }

    protected function getAdoConnection(): ADOConnection
    {
        return adoNewConnection('pdo');
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     */
    public function getConnection(): ADOConnection
    {
        $dataSourceName = $this->getDataSourceName();
        $connection = $this->getAdoConnection();

        $connection->pConnect(
            $dataSourceName->getConnectionString(), $dataSourceName->getUsername(), $dataSourceName->getPassword()
        );

        $connection->SetFetchMode(ADODB_FETCH_ASSOC);

        try
        {
            return $connection;
        }
        catch (Exception $exception)
        {
            throw new ConnectionException(
                'Could not connect to the database. Please contact your system administrator.'
            );
        }
    }

    public function getDataSourceName(): DataSourceName
    {
        return $this->dataSourceName;
    }

    public function setDataSourceName(DataSourceName $dataSourceName): ConnectionFactory
    {
        $this->dataSourceName = $dataSourceName;

        return $this;
    }
}
