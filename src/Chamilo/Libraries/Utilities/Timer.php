<?php
namespace Chamilo\Libraries\Utilities;

/**
 * Class to time a script
 *
 * @package Chamilo\Libraries\Utilities
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Timer
{

    private int $startTime;

    private int $stopTime;

    public function __construct()
    {
        $this->reset();
    }

    private function getMicrotime(): float
    {
        list($usec, $sec) = explode(" ", microtime());

        return ((float) $usec + (float) $sec);
    }

    /**
     * Returns the difference between the stop and start time in seconds
     */
    public function getTime(): int
    {
        return (int) ($this->stopTime - $this->startTime);
    }

    /**
     * Returns the difference between the stop and start time in hours:minutes:seconds
     */
    public function getTimeInHours(): string
    {
        return DatetimeUtilities::convert_seconds_to_hours($this->getTime());
    }

    /**
     * Reset the start and stop time
     */
    public function reset()
    {
        $this->startTime = 0;
        $this->stopTime = 0;
    }

    /**
     * Starts the timer by setting the start time to the current microtime
     */
    public function start()
    {
        $this->startTime = $this->getMicrotime();
    }

    /**
     * Stops the timer by setting the stop time to the current microtime
     */
    public function stop()
    {
        $this->stopTime = $this->getMicrotime();
    }
}
