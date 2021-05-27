<?php
namespace Chamilo\Core\Home\Storage\DataClass;

use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Home\Storage\DataClass-
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Column extends Element
{
    const CONFIGURATION_WIDTH = 'width';

    /**
     *
     * @param string[] $configurationVariables
     *
     * @return string[]
     */
    public static function getConfigurationVariables($configurationVariables = [])
    {
        return parent::getConfigurationVariables(array(self::CONFIGURATION_WIDTH));
    }

    /**
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->getSetting(self::CONFIGURATION_WIDTH);
    }

    public function is_empty()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Block::class, Block::PROPERTY_PARENT_ID),
            new StaticConditionVariable($this->get_id())
        );

        $blocks_count = DataManager::count(Block::class, new DataClassCountParameters($condition));

        return ($blocks_count == 0);
    }

    /**
     *
     * @param integer $width
     */
    public function setWidth($width)
    {
        $this->setSetting(self::CONFIGURATION_WIDTH, $width);
    }
}
