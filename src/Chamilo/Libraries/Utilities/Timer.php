<?php
namespace Chamilo\Libraries\Utilities;

/**
 * Class to time a script
 * 
 * @author svenvanpoucke
 */
class Timer
{

    private $start_time;

    private $stop_time;

    public function __construct()
    {
        $this->reset();
    }

    /**
     * Reset the start and stop time
     */
    public function reset()
    {
        $this->start_time = 0;
        $this->stop_time = 0;
    }

    /**
     * Starts the timer by setting the start time to the current microtime
     */
    public function start()
    {
        $this->start_time = $this->get_microtime();
    }

    /**
     * Stops the timer by setting the stop time to the current microtime
     */
    public function stop()
    {
        $this->stop_time = $this->get_microtime();
    }

    /**
     * Returns the difference between the stop and start time in seconds
     */
    public function get_time()
    {
        return (int) ($this->stop_time - $this->start_time);
    }

    /**
     * Returns the difference between the stop and start time in hours:minutes:seconds
     */
    public function get_time_in_hours()
    {
        return DatetimeUtilities::convert_seconds_to_hours($this->get_time());
    }

    /**
     * function to get the microtime
     */
    private function get_microtime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }
}
