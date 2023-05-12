<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment;

use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with an overview of scores of each assessment per
 *          user
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class AssessmentUserScoresBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $course_id = $this->getCourseId();

        $users = CourseDataManager::retrieve_all_course_users($course_id);

        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID
            ), new StaticConditionVariable($course_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TOOL
            ), new StaticConditionVariable(
                ClassnameUtilities::getInstance()->getClassNameFromNamespace(Assessment::class, true)
            )
        );
        $condition = new AndCondition($conditions);

        $order_by = array(
            new OrderProperty(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_MODIFIED_DATE
                )
            )
        );

        $publication_resultset = DataManager::retrieves(
            ContentObjectPublication::class,
            new DataClassRetrievesParameters($condition, null, null, new OrderBy($order_by))
        );

        $publications = [];
        $headings = [];
        $headings[] = Translation::get('Name');
        $headings[] = Translation::get('OfficialCode', null, Manager::CONTEXT);
        foreach ($publication_resultset as $publication)
        {
            $publications[] = $publication;

            $content_object = $publication->get_content_object();

            if ($publication_resultset->count() > 5)
            {
                $headings[] = substr($content_object->get_title(), 0, 14);
            }
            else
            {
                $headings[] = $content_object->get_title();
            }
        }

        $passingPercentage = Configuration::getInstance()->get_setting(
            array('Chamilo\Core\Admin', 'passing_percentage')
        );

        $reporting_data->set_rows($headings);

        foreach ($users as $key => $user)
        {
            $reporting_data->add_category($key);
            $reporting_data->add_data_category_row(
                $key, Translation::get('Name'), User::fullname(
                $user[User::PROPERTY_FIRSTNAME], $user[User::PROPERTY_LASTNAME]
            )
            );

            $reporting_data->add_data_category_row(
                $key, Translation::get('OfficialCode', null, Manager::CONTEXT), $user[User::PROPERTY_OFFICIAL_CODE]
            );

            foreach ($publications as $publication)
            {
                $content_object = $publication->get_content_object();

                if ($publication_resultset->count() > 5)
                {
                    $title = substr($content_object->get_title(), 0, 14);
                }
                else
                {
                    $title = $content_object->get_title();
                }

                $conditions = [];

                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        AssessmentAttempt::class, AssessmentAttempt::PROPERTY_ASSESSMENT_ID
                    ), new StaticConditionVariable($publication->get_id())
                );

                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        AssessmentAttempt::class, AssessmentAttempt::PROPERTY_USER_ID
                    ), new StaticConditionVariable($user[User::PROPERTY_ID])
                );
                $condition = new AndCondition($conditions);

                $attempts_by_user = \Chamilo\Libraries\Storage\DataManager\DataManager::retrieves(
                    AssessmentAttempt::class, new DataClassRetrievesParameters($condition)
                );

                if ($attempts_by_user->count() == 0)
                {
                    if (DataManager::is_publication_target_user(
                        $user[User::PROPERTY_ID], $publication->get_id(), $course_id
                    ))
                    {
                        $reporting_data->add_data_category_row($key, $title, null);
                        continue;
                    }

                    $reporting_data->add_data_category_row($key, $title, 'X');
                    continue;
                }

                $score = $this->get_score($attempts_by_user);
                if ($score < $passingPercentage)
                {
                    $score = '<span style="color:red">' . $score . '</span>';
                }
                else
                {
                    $score = '<span style="color:green">' . $score . '</span>';
                }
                $reporting_data->add_data_category_row($key, $title, $score);
            }
        }
        $reporting_data->hide_categories();

        return $reporting_data;
    }

    private function get_score($attempts)
    {
        $score_type = (Request::post('sel')) ? Request::post('sel') : Request::get('sel');
        if (is_null($score_type))
        {
            $score_type = Request::get('sel');
        }
        $score = null;

        switch ($score_type)
        {
            case self::SCORE_TYPE_AVG :
                foreach ($attempts as $attempt)
                {
                    $score += $attempt->get_total_score();
                }

                return number_format($score / count($attempts), 1);
            case self::SCORE_TYPE_MIN :
                foreach ($attempts as $attempt)
                {
                    if (is_null($score) || $attempt->get_total_score() < $score)
                    {
                        $score = $attempt->get_total_score();
                    }
                }

                return $score;
            case self::SCORE_TYPE_MAX :
                foreach ($attempts as $attempt)
                {
                    if (is_null($score) || $attempt->get_total_score() > $score)
                    {
                        $score = $attempt->get_total_score();
                    }
                }

                return $score;
            case self::SCORE_TYPE_FIRST :
                $date = null;
                foreach ($attempts as $attempt)
                {
                    if (is_null($score) || $attempt->get_start_time() < $date)
                    {
                        $date = $attempt->get_start_time();
                        $score = $attempt->get_total_score();
                    }
                }

                return $score;
            case self::SCORE_TYPE_LAST :
                $date = null;
                foreach ($attempts as $attempt)
                {
                    if (is_null($score) || $attempt->get_start_time() > $date)
                    {
                        $date = $attempt->get_start_time();
                        $score = $attempt->get_total_score();
                    }
                }

                return $score;
            default :
                foreach ($attempts as $attempt)
                {
                    $score += $attempt->get_total_score();
                }

                return number_format($score / count($attempts), 1);
        }
    }

    public function get_views()
    {
        return array(Html::VIEW_TABLE);
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }
}
