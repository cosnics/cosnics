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

    private float $startTime;

    private float $stopTime;

    public function __construct()
    {
        $this->reset();
    }

    /**
     * Returns the difference between the stop and start time in seconds
     */
    public function getDurationInSeconds(): float
    {
        return $this->stopTime - $this->startTime;
    }

    /**
     * Returns the difference between the stop and start time in hours:minutes:seconds
     */
    public function getHumanReadableDuration(): string
    {
        if ($this->getDurationInSeconds() / 3600 < 1 && $this->getDurationInSeconds() / 60 < 1)
        {
            $convertedTime = number_format($this->getDurationInSeconds(),3). 's';
        }
        else
        {
            if ($this->getDurationInSeconds() / 3600 < 1)
            {
                $minutes = (int) ($this->getDurationInSeconds() / 60);
                $seconds = $this->getDurationInSeconds() % 60;
                $convertedTime = $minutes . 'm ' . number_format($seconds, 3) . 's';
            }
            else
            {
                $hours = (int) ($this->getDurationInSeconds() / 3600);
                $rest = $this->getDurationInSeconds() % 3600;
                $minutes = (int) ($rest / 60);
                $seconds = $rest % 60;
                $convertedTime = $hours . 'h ' . $minutes . 'm ' . number_format($seconds, 3) . 's';
            }
        }

        return $convertedTime;
    }

    private function getMicrotime(): float
    {
        return microtime(true);
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
