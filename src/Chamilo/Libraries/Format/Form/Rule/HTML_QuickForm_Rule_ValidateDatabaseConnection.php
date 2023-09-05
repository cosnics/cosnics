<?php
namespace Chamilo\Libraries\Format\Form\Rule;

use Chamilo\Libraries\Storage\DataManager\Doctrine\DataSourceName;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory;
use Exception;
use HTML_QuickForm_Rule;

/**
 * @package Chamilo\Libraries\Format\Form\Rule
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HTML_QuickForm_Rule_ValidateDatabaseConnection extends HTML_QuickForm_Rule
{

    public function validate($value, $options = null): bool
    {
        $settings = [
            'driver' => $value[0],
            'username' => $value[2],
            'password' => $value[3],
            'host' => $value[1],
            'name' => $value[4],
            'charset' => 'utf8'
        ];

        $connectionFactory = new ConnectionFactory(new DataSourceName($settings));

        try
        {
            $connectionFactory->getConnection();

            return true;
        }
        catch (Exception)
        {
            return false;
        }
    }
}