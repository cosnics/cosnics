<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

class LpiAttemptObjective extends \Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker
{
    const PROPERTY_LPI_VIEW_ID = 'lpi_view_id';
    const PROPERTY_OBJECTIVE_ID = 'objective_id';
    const PROPERTY_SCORE_RAW = 'score_raw';
    const PROPERTY_SCORE_MAX = 'score_max';
    const PROPERTY_SCORE_MIN = 'score_min';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_DISPLAY_ORDER = 'display_order';

    public function validate_parameters(array $parameters = array())
    {
        $this->set_lpi_view_id($parameters[self::PROPERTY_LPI_VIEW_ID]);
        $this->set_objective_id($parameters[self::PROPERTY_OBJECTIVE_ID]);
        $this->set_score_raw($parameters[self::PROPERTY_SCORE_RAW]);
        $this->set_score_max($parameters[self::PROPERTY_SCORE_MAX]);
        $this->set_score_min($parameters[self::PROPERTY_SCORE_MIN]);
        $this->set_status($parameters[self::PROPERTY_STATUS]);
        $this->set_display_order($parameters[self::PROPERTY_DISPLAY_ORDER]);
    }

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
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
        return $this->get_default_property(self::PROPERTY_LPI_VIEW_ID);
    }

    public function set_lpi_view_id($lpi_view_id)
    {
        $this->set_default_property(self::PROPERTY_LPI_VIEW_ID, $lpi_view_id);
    }

    public function get_objective_id()
    {
        return $this->get_default_property(self::PROPERTY_OBJECTIVE_ID);
    }

    public function set_objective_id($objective_id)
    {
        $this->set_default_property(self::PROPERTY_OBJECTIVE_ID, $objective_id);
    }

    public function get_score_raw()
    {
        return $this->get_default_property(self::PROPERTY_SCORE_RAW);
    }

    public function set_score_raw($score_raw)
    {
        $this->set_default_property(self::PROPERTY_SCORE_RAW, $score_raw);
    }

    public function get_score_max()
    {
        return $this->get_default_property(self::PROPERTY_SCORE_MAX);
    }

    public function set_score_max($score_max)
    {
        $this->set_default_property(self::PROPERTY_SCORE_MAX, $score_max);
    }

    public function get_score_min()
    {
        return $this->get_default_property(self::PROPERTY_SCORE_MIN);
    }

    public function set_score_min($score_min)
    {
        $this->set_default_property(self::PROPERTY_SCORE_MIN, $score_min);
    }

    public function get_status()
    {
        return $this->get_default_property(self::PROPERTY_STATUS);
    }

    public function set_status($status)
    {
        $this->set_default_property(self::PROPERTY_STATUS, $status);
    }

    public function get_display_order()
    {
        return $this->get_default_property(self::PROPERTY_DISPLAY_ORDER);
    }

    public function set_display_order($display_order)
    {
        $this->set_default_property(self::PROPERTY_DISPLAY_ORDER, $display_order);
    }
}
