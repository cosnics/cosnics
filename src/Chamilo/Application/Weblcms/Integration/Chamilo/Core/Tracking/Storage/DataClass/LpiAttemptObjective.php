<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker;

class LpiAttemptObjective extends SimpleTracker
{
    const PROPERTY_LPI_VIEW_ID = 'lpi_view_id';
    const PROPERTY_OBJECTIVE_ID = 'objective_id';
    const PROPERTY_SCORE_RAW = 'score_raw';
    const PROPERTY_SCORE_MAX = 'score_max';
    const PROPERTY_SCORE_MIN = 'score_min';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_DISPLAY_ORDER = 'display_order';

    public function validate_parameters(array $parameters = [])
    {
        $this->set_lpi_view_id($parameters[self::PROPERTY_LPI_VIEW_ID]);
        $this->set_objective_id($parameters[self::PROPERTY_OBJECTIVE_ID]);
        $this->set_score_raw($parameters[self::PROPERTY_SCORE_RAW]);
        $this->set_score_max($parameters[self::PROPERTY_SCORE_MAX]);
        $this->set_score_min($parameters[self::PROPERTY_SCORE_MIN]);
        $this->set_status($parameters[self::PROPERTY_STATUS]);
        $this->set_display_order($parameters[self::PROPERTY_DISPLAY_ORDER]);
    }

    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(
                self::PROPERTY_LPI_VIEW_ID, 
                self::PROPERTY_OBJECTIVE_ID, 
                self::PROPERTY_SCORE_RAW, 
                self::PROPERTY_SCORE_MAX, 
                self::PROPERTY_SCORE_MIN, 
                self::PROPERTY_STATUS, 
                self::PROPERTY_DISPLAY_ORDER));
    }

    public function get_lpi_view_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_LPI_VIEW_ID);
    }

    public function set_lpi_view_id($lpi_view_id)
    {
        $this->setDefaultProperty(self::PROPERTY_LPI_VIEW_ID, $lpi_view_id);
    }

    public function get_objective_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_OBJECTIVE_ID);
    }

    public function set_objective_id($objective_id)
    {
        $this->setDefaultProperty(self::PROPERTY_OBJECTIVE_ID, $objective_id);
    }

    public function get_score_raw()
    {
        return $this->getDefaultProperty(self::PROPERTY_SCORE_RAW);
    }

    public function set_score_raw($score_raw)
    {
        $this->setDefaultProperty(self::PROPERTY_SCORE_RAW, $score_raw);
    }

    public function get_score_max()
    {
        return $this->getDefaultProperty(self::PROPERTY_SCORE_MAX);
    }

    public function set_score_max($score_max)
    {
        $this->setDefaultProperty(self::PROPERTY_SCORE_MAX, $score_max);
    }

    public function get_score_min()
    {
        return $this->getDefaultProperty(self::PROPERTY_SCORE_MIN);
    }

    public function set_score_min($score_min)
    {
        $this->setDefaultProperty(self::PROPERTY_SCORE_MIN, $score_min);
    }

    public function get_status()
    {
        return $this->getDefaultProperty(self::PROPERTY_STATUS);
    }

    public function set_status($status)
    {
        $this->setDefaultProperty(self::PROPERTY_STATUS, $status);
    }

    public function get_display_order()
    {
        return $this->getDefaultProperty(self::PROPERTY_DISPLAY_ORDER);
    }

    public function set_display_order($display_order)
    {
        $this->setDefaultProperty(self::PROPERTY_DISPLAY_ORDER, $display_order);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'tracking_weblcms_lpi_attempt_objective';
    }
}
