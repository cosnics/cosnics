<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker;

class LpiAttemptInteraction extends SimpleTracker
{
    public const CONTEXT = 'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking';

    public const PROPERTY_COMPLETION_TIME = 'completion_time';
    public const PROPERTY_CORRECT_RESPONSES = 'correct_responses';
    public const PROPERTY_DISPLAY_ORDER = 'display_order';
    public const PROPERTY_INTERACTION_ID = 'interaction_id';
    public const PROPERTY_INTERACTION_TYPE = 'interaction_type';
    public const PROPERTY_LATENCY = 'latency';
    public const PROPERTY_LPI_VIEW_ID = 'lpi_view_id';
    public const PROPERTY_RESULT = 'result';
    public const PROPERTY_STUDENT_RESPONSES = 'student_responses';
    public const PROPERTY_WEIGHT = 'weight';

    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_LPI_VIEW_ID,
                self::PROPERTY_INTERACTION_ID,
                self::PROPERTY_INTERACTION_TYPE,
                self::PROPERTY_WEIGHT,
                self::PROPERTY_COMPLETION_TIME,
                self::PROPERTY_CORRECT_RESPONSES,
                self::PROPERTY_STUDENT_RESPONSES,
                self::PROPERTY_RESULT,
                self::PROPERTY_LATENCY,
                self::PROPERTY_DISPLAY_ORDER
            ]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'tracking_weblcms_lpi_attempt_interaction';
    }

    public function get_completion_time()
    {
        return $this->getDefaultProperty(self::PROPERTY_COMPLETION_TIME);
    }

    public function get_correct_responses()
    {
        return $this->getDefaultProperty(self::PROPERTY_CORRECT_RESPONSES);
    }

    public function get_display_order()
    {
        return $this->getDefaultProperty(self::PROPERTY_DISPLAY_ORDER);
    }

    public function get_interaction_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_INTERACTION_ID);
    }

    public function get_interaction_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_INTERACTION_TYPE);
    }

    public function get_latency()
    {
        return $this->getDefaultProperty(self::PROPERTY_LATENCY);
    }

    public function get_lpi_view_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_LPI_VIEW_ID);
    }

    public function get_result()
    {
        return $this->getDefaultProperty(self::PROPERTY_RESULT);
    }

    public function get_student_responses()
    {
        return $this->getDefaultProperty(self::PROPERTY_STUDENT_RESPONSES);
    }

    public function get_weight()
    {
        return $this->getDefaultProperty(self::PROPERTY_WEIGHT);
    }

    public function set_completion_time($completion_time)
    {
        $this->setDefaultProperty(self::PROPERTY_COMPLETION_TIME, $completion_time);
    }

    public function set_correct_responses($correct_responses)
    {
        $this->setDefaultProperty(self::PROPERTY_CORRECT_RESPONSES, $correct_responses);
    }

    public function set_display_order($display_order)
    {
        $this->setDefaultProperty(self::PROPERTY_DISPLAY_ORDER, $display_order);
    }

    public function set_interaction_id($interaction_id)
    {
        $this->setDefaultProperty(self::PROPERTY_INTERACTION_ID, $interaction_id);
    }

    public function set_interaction_type($interaction_type)
    {
        $this->setDefaultProperty(self::PROPERTY_INTERACTION_TYPE, $interaction_type);
    }

    public function set_latency($latency)
    {
        $this->setDefaultProperty(self::PROPERTY_LATENCY, $latency);
    }

    public function set_lpi_view_id($lpi_view_id)
    {
        $this->setDefaultProperty(self::PROPERTY_LPI_VIEW_ID, $lpi_view_id);
    }

    public function set_result($result)
    {
        $this->setDefaultProperty(self::PROPERTY_RESULT, $result);
    }

    public function set_student_responses($student_responses)
    {
        $this->setDefaultProperty(self::PROPERTY_STUDENT_RESPONSES, $student_responses);
    }

    public function set_weight($weight)
    {
        $this->setDefaultProperty(self::PROPERTY_WEIGHT, $weight);
    }

    public function validate_parameters(array $parameters = [])
    {
        $this->set_lpi_view_id($parameters[self::PROPERTY_LPI_VIEW_ID]);
        $this->set_interaction_id($parameters[self::PROPERTY_INTERACTION_ID]);
        $this->set_interaction_type($parameters[self::PROPERTY_INTERACTION_TYPE]);
        $this->set_weight($parameters[self::PROPERTY_WEIGHT]);
        $this->set_completion_time($parameters[self::PROPERTY_COMPLETION_TIME]);
        $this->set_correct_responses($parameters[self::PROPERTY_CORRECT_RESPONSES]);
        $this->set_student_responses($parameters[self::PROPERTY_STUDENT_RESPONSES]);
        $this->set_result($parameters[self::PROPERTY_RESULT]);
        $this->set_latency($parameters[self::PROPERTY_LATENCY]);
        $this->set_display_order($parameters[self::PROPERTY_DISPLAY_ORDER]);
    }
}
