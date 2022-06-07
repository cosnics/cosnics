<?php
namespace Chamilo\Core\Repository\ContentObject\Task;

use Chamilo\Core\Repository\Common\ContentObjectDifference;
use Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This class can be used to get the difference between tasks
 */
class TaskDifference extends ContentObjectDifference
{
    const PROPERTY_DATES = 'dates';

    /**
     * @return string[]
     */
    public function getAdditionalPropertyNames()
    {
        return array(
            self::PROPERTY_DATES,
            Task::PROPERTY_CATEGORY,
            Task::PROPERTY_PRIORITY,
            Task::PROPERTY_FREQUENCY
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task $contentObject
     *
     * @return string
     */
    public function getDatesString(ContentObject $contentObject)
    {
        $dateFormat = Translation::get('DateTimeFormatLong', null, StringUtilities::LIBRARIES);

        return Translation::get(
            'TaskDate', array(
                'START' => DatetimeUtilities::getInstance()->formatLocaleDate($dateFormat, $contentObject->get_start_date()),
                'END' => DatetimeUtilities::getInstance()->formatLocaleDate($dateFormat, $contentObject->get_due_date())
            )
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task $contentObject
     *
     * @return string
     */
    public function getFrequencyString(ContentObject $contentObject)
    {
        $repeats = $contentObject->has_frequency();
        $content = [];

        if ($repeats)
        {
            switch ($contentObject->get_frequency())
            {

                case 1 :
                    if ($contentObject->get_frequency_interval() == 1)
                    {
                        $content[] = Translation::get('EveryDay');
                    }
                    else
                    {
                        $content[] =
                            Translation::get('EveryXDays', array('DAYS' => $contentObject->get_frequency_interval()));
                    }
                    break;
                case 2 :
                    $days = [];
                    foreach (explode(',', $contentObject->get_byday()) as $day)
                    {
                        $days[] = Task::get_day_string($day);
                    }
                    if ($contentObject->get_frequency_interval() == 1)
                    {
                        $content[] = Translation::get('EveryWeek', array('DAYS' => implode(', ', $days)));
                    }
                    else
                    {
                        $content[] = Translation::get(
                            'EveryXWeeksOnY',
                            array('WEEKS' => $contentObject->get_frequency_interval(), 'DAYS' => implode(', ', $days))
                        );
                    }
                    break;
                case 5 :
                    if ($contentObject->get_bymonthday())
                    {
                        if ($contentObject->get_frequency_interval() == 1)
                        {
                            if (count($contentObject->get_bymonthday()) > 1)
                            {
                                $content[] = Translation::get(
                                    'EveryMonthOnDaysY', array('DAYS' => $contentObject->get_bymonthday())
                                );
                            }
                            else
                            {
                                $content[] = Translation::get(
                                    'EveryMonthOnDayY', array('DAY' => $contentObject->get_bymonthday())
                                );
                            }
                        }
                        else
                        {
                            if (count($contentObject->get_bymonthday()) > 1)
                            {
                                $content[] = Translation::get(
                                    'EveryXMonthsOnDaysY', array(
                                        'MONTHS' => $contentObject->get_frequency_interval(),
                                        'DAYS' => $contentObject->get_bymonthday()
                                    )
                                );
                            }
                            else
                            {
                                $content[] = Translation::get(
                                    'EveryXMonthsOnDayY', array(
                                        'MONTHS' => $contentObject->get_frequency_interval(),
                                        'DAY' => $contentObject->get_bymonthday()
                                    )
                                );
                            }
                        }
                    }
                    else
                    {
                        $byday = Task::get_byday_parts($contentObject->get_byday());
                        $byday = $byday[0];
                        if ($contentObject->get_frequency_interval() == 1)
                        {
                            $content[] = Translation::get(
                                'EveryMonthOnRankDay', array(
                                    'RANK' => Task::get_rank_string($byday[0]),
                                    'DAY' => Task::get_day_string($byday[1])
                                )
                            );
                        }
                        else
                        {
                            $content[] = Translation::get(
                                'EveryXMonthsOnRankDay', array(
                                    'MONTHS' => $contentObject->get_frequency_interval(),
                                    'RANK' => Task::get_rank_string($byday[0]),
                                    'DAY' => Task::get_day_string($byday[1])
                                )
                            );
                        }
                    }
                    break;
                case 6 :
                    if ($contentObject->get_bymonthday())
                    {
                        if ($contentObject->get_frequency_interval() == 1)
                        {
                            if (count($contentObject->get_bymonthday()) > 1)
                            {
                                $content[] = Translation::get(
                                    'EveryYearOnDaysYOfMonthZ', array(
                                        'YEARS' => $contentObject->get_frequency_interval(),
                                        'DAYS' => $contentObject->get_bymonthday(),
                                        'MONTH' => Task::get_bymonth_string($contentObject->get_bymonth())
                                    )
                                );
                            }
                            else
                            {
                                $content[] = Translation::get(
                                    'EveryYearOnDayOfMonthZ', array(
                                        'DAYS' => $contentObject->get_bymonthday(),
                                        'MONTH' => Task::get_bymonth_string($contentObject->get_bymonth())
                                    )
                                );
                            }
                        }
                        else
                        {
                            if (count($contentObject->get_bymonthday()) > 1)
                            {
                                $content[] = Translation::get(
                                    'EveryXYearsOnDaysYOfMonthZ', array(
                                        'YEARS' => $contentObject->get_frequency_interval(),
                                        'DAYS' => $contentObject->get_bymonthday(),
                                        'MONTH' => Task::get_bymonth_string($contentObject->get_bymonth())
                                    )
                                );
                            }
                            else
                            {
                                $content[] = Translation::get(
                                    'EveryXYearsOnDayOfMonthZ', array(
                                        'YEARS' => $contentObject->get_frequency_interval(),
                                        'DAYS' => $contentObject->get_bymonthday(),
                                        'MONTH' => Task::get_bymonth_string($contentObject->get_bymonth())
                                    )
                                );
                            }
                        }
                    }
                    else
                    {
                        if ($contentObject->get_frequency_interval() == 1)
                        {
                            $byday = Task::get_byday_parts($contentObject->get_byday());
                            $byday = $byday[0];
                            $content[] = Translation::get(
                                'EveryYearOnRankDayOfMonthZ', array(
                                    'RANK' => Task::get_rank_string($byday[0]),
                                    'DAY' => Task::get_day_string($byday[1]),
                                    'MONTH' => Task::get_bymonth_string($contentObject->get_bymonth())
                                )
                            );
                        }
                        else
                        {
                            $byday = Task::get_byday_parts($contentObject->get_byday());
                            $byday = $byday[0];
                            $content[] = Translation::get(
                                'EveryXYearsOnRankDayOfMonthZ', array(
                                    'YEARS' => $contentObject->get_frequency_interval(),
                                    'RANK' => Task::get_rank_string($byday[0]),
                                    'DAY' => Task::get_day_string($byday[1]),
                                    'MONTH' => Task::get_bymonth_string($contentObject->get_bymonth())
                                )
                            );
                        }
                    }
                    break;
            }

            if ($contentObject->get_frequency_count() || $contentObject->get_until())
            {
                $content[] = PHP_EOL;
                $dateFormat = Translation::get('DateTimeFormatLong', null, StringUtilities::LIBRARIES);

                if ($contentObject->get_frequency_count())
                {
                    $content[] =
                        Translation::get('OccursXTimes', array('TIMES' => $contentObject->get_frequency_count()));
                }
                if ($contentObject->get_until())
                {
                    $content[] = Translation::get(
                        'RepeatUntilDate', array(
                            'DATE' => DatetimeUtilities::getInstance()->formatLocaleDate(
                                $dateFormat, $contentObject->get_until()
                            )
                        )
                    );
                }
            }
        }

        return implode('', $content);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param string $propertyName
     *
     * @return string[]
     */
    public function getVisualAdditionalPropertyValue(ContentObject $contentObject, string $propertyName)
    {
        switch ($propertyName)
        {
            case self::PROPERTY_DATES:
                $content = $this->getDatesString($contentObject);
                break;
            case Task::PROPERTY_FREQUENCY:
                $content = $this->getFrequencyString($contentObject);
                break;
            default:
                $content = parent::getVisualAdditionalPropertyValue($contentObject, $propertyName);
        }

        return explode(PHP_EOL, $content);
    }
}
