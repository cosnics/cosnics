<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingBlock;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

abstract class Block extends ReportingBlock
{

    public function get_user_id()
    {
        return $this->get_parent()->get_parameter(\Chamilo\Core\User\Manager :: PARAM_USER_USER_ID);
    }

    public static function getDateArray($data, $format)
    {
        $login_dates = array();

        while ($login_date = $data->next_result())
        {
            $date = date($format, $login_date->get_date());

            if (array_key_exists($date, $login_dates))
            {
                $login_dates[$date] ++;
            }
            else
            {
                $login_dates[$date] = 1;
            }
        }

        return $login_dates;
    }

    /**
     * Generates an array from a tracker Currently only supports 1 serie
     *
     * @todo support multiple series
     * @param Tracker $tracker
     * @return array
     */
    public static function array_from_tracker($tracker, $condition = null, $description = null)
    {
        $c = 0;
        $array = array();

        $trackerdata = DataManager :: retrieves($tracker :: class_name(), new DataClassRetrievesParameters($condition))->as_array();

        foreach ($trackerdata as $key => $value)
        {
            $arr[$value->get_name()] = $value->get_value();
        }
        return $arr;
    }
}
