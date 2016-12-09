<?php
namespace Chamilo\Libraries\Storage\DataManager\Interfaces;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
interface DataSourceNameFactoryInterface
{

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\DataSourceName
     */
    public function getDataSourceName();
}
