<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\ExportFormat;

use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 * Export format for the progress of users
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserTreeNodeAttemptExportFormat implements ExportFormatInterface
{
    /**
     * @var int
     */
    protected $startTime;

    /**
     * @var bool
     */
    protected $completed;

    /**
     * @var int
     */
    protected $score;

    /**
     * @var string
     */
    protected $time;

    /**
     * UserTreeNodeAttemptExportFormat constructor.
     *
     * @param int $startTime
     * @param bool $completed
     * @param int $score
     * @param string $time
     */
    public function __construct($startTime, $completed, $score, $time)
    {
        $this->startTime = $startTime;
        $this->completed = $completed;
        $this->score = $score;
        $this->time = $time;
    }

    /**
     * Converts the export format to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        $exportData = [
            'start_time' => DatetimeUtilities::getInstance()->formatLocaleDate(null, $this->startTime),
            'completed' => (string) $this->completed,
            'time' => DatetimeUtilities::getInstance()->formatSecondsToHours($this->time),
        ];

        if(!is_null($this->score))
        {
            $exportData['score'] = $this->score . '%';
        }

        return $exportData;
    }
}
