<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupUserRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\UserExporter\CourseGroupUserExportExtender;
use Chamilo\Application\Weblcms\UserExporter\Renderer\ExcelUserExportRenderer;
use Chamilo\Application\Weblcms\UserExporter\UserExporter;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Color;
use PHPExcel_Style_Font;

/**
 * Class To export users and course groups from a course
 *
 * @author VUB - Original author
 * @author Sven Vanpoucke - Bugfixes, comments
 */
class ExporterComponent extends Manager
{

    private $current_tab;

    private $course_group;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        if (Request:: get(self :: PARAM_COURSE_GROUP))
        {
            $course_group_id = Request:: get(self :: PARAM_COURSE_GROUP);

            $course_group = DataManager:: retrieve_by_id(CourseGroup:: class_name(), $course_group_id);

            $this->course_group = $course_group;
        }

        $this->current_tab = SubscriptionsOverviewerComponent :: TAB_USERS;
        if (Request:: get(SubscriptionsOverviewerComponent :: PARAM_TAB))
        {
            $this->current_tab = Request:: get(SubscriptionsOverviewerComponent :: PARAM_TAB);
        }
        elseif (!is_null($this->course_group))
        {
            $this->current_tab = SubscriptionsOverviewerComponent :: TAB_COURSE_GROUPS;
        }

        switch ($this->current_tab)
        {
            case SubscriptionsOverviewerComponent :: TAB_USERS :
                $file_path = $this->export_users();
                break;
            case SubscriptionsOverviewerComponent :: TAB_COURSE_GROUPS :
                $file_path = $this->render();
                break;
        }

        $this->send_as_download($file_path);
        Filesystem:: remove($file_path);
    }

    /**
     * Exports the users with the new exporter
     *
     * @return string
     */
    public function export_users()
    {
        $user_records = CourseDataManager:: retrieve_all_course_users(
            $this->get_course_id(), null, null, null,
            array(
                new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME)),
                new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME))
            )
        );

        $users = array();

        while ($user_record = $user_records->next_result())
        {
            $users[] = DataClass:: factory(User:: class_name(), $user_record);
        }

        $exporter = new UserExporter(
            new ExcelUserExportRenderer(),
            array(new CourseGroupUserExportExtender($this->get_course_id()))
        );

        return $exporter->export($users);
    }

    /**
     * Sends the exported file as download
     *
     * @param $file_path string
     */
    public function send_as_download($file_path)
    {
        // Determines the filename that the user will see when they download the
        // file
        $course = $this->get_course();

        if ($this->course_group)
        {
            $filename = $course->get_title() . '_' . $this->course_group->get_name() . "_" . date('Ymd') . '.xlsx';
        }
        else
        {
            $filename = $course->get_title() . date('Ymd') . '.xlsx';
        }

        // Make safe name
        $filename = Filesystem:: create_safe_name($filename);
        $filename = preg_replace('/[\s]+/', '_', $filename);

        $type = 'application/vnd.openxmlformats';

        // Send file for download
        Filesystem:: file_send_for_download($file_path, true, $filename, $type);
    }

    /**
     * Returns the conditions when the course groups need to be exported
     *
     * @return AndCondition
     */
    public function get_condition()
    {
        $conditions = array();

        if ($this->current_tab == SubscriptionsOverviewerComponent :: TAB_COURSE_GROUPS)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseGroup:: class_name(), CourseGroup :: PROPERTY_COURSE_CODE),
                new StaticConditionVariable($this->get_course_id())
            );
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(CourseGroup:: class_name(), CourseGroup :: PROPERTY_PARENT_ID),
                    new StaticConditionVariable(0)
                )
            );
        }

        if ($conditions)
        {
            return new AndCondition($conditions);
        }

        return null;
    }

    /**
     * Gets the export data for the users
     *
     * @param $data User
     *
     * @return array
     */
    private function get_users_table($data)
    {
        $table = array();
        $table[0][User :: PROPERTY_OFFICIAL_CODE] = Translation:: get(
            'OfficialCode',
            null,
            \Chamilo\Core\User\Manager:: context()
        );
        $table[0][User :: PROPERTY_USERNAME] = Translation:: get(
            'Username',
            null,
            \Chamilo\Core\User\Manager:: context()
        );
        $table[0][User :: PROPERTY_LASTNAME] = Translation:: get(
            'Lastname',
            null,
            \Chamilo\Core\User\Manager:: context()
        );
        $table[0][User :: PROPERTY_FIRSTNAME] = Translation:: get(
            'Firstname',
            null,
            \Chamilo\Core\User\Manager:: context()
        );

        $table[0][User :: PROPERTY_EMAIL] = Translation:: get('Email', null, \Chamilo\Core\User\Manager:: context());
        $table[0][CourseGroupUserRelation::PROPERTY_SUBSCRIPTION_TIME] = Translation:: get('SubscriptionTime');
        $table[0]['Course Groups'] = Translation:: get('CourseGroups');

        $index = 0;
        while ($block_data = $data->next_result())
        {
//            if (!$block_data instanceof User)
//            {
//                $block_data = DataClass:: factory(User:: class_name(), $block_data);
//            }

            $index ++;
            // get the list of course_groups the user is subscribed
            $course_groups = DataManager:: retrieve_course_groups_from_user(
                $block_data[User::PROPERTY_ID],
                $this->get_course_id()
            );
            $course_groups_subscribed = array();
            while ($course_group = $course_groups->next_result())
            {
                $course_groups_subscribed[] = $course_group->get_name();
            }
            $course_groups_string = null;
            foreach ($course_groups_subscribed as $item)
            {
                if ($course_groups_string)
                {
                    $course_groups_string = $course_groups_string . ', ' . $item;
                }
                else
                {
                    $course_groups_string = $item;
                }
            }

            $table[$index][User :: PROPERTY_OFFICIAL_CODE] = $block_data[User::PROPERTY_OFFICIAL_CODE];
            $table[$index][User :: PROPERTY_USERNAME] = $block_data[User::PROPERTY_USERNAME];
            $table[$index][User :: PROPERTY_LASTNAME] = $block_data[User::PROPERTY_LASTNAME];
            $table[$index][User :: PROPERTY_FIRSTNAME] = $block_data[User::PROPERTY_FIRSTNAME];
            $table[$index][User :: PROPERTY_EMAIL] = $block_data[User::PROPERTY_EMAIL];
            $table[$index][CourseGroupUserRelation::PROPERTY_SUBSCRIPTION_TIME] =
                DatetimeUtilities::format_locale_date(
                    Translation::getInstance()->getTranslation('SubscriptionTimeFormat', null, Manager::context()),
                    $block_data[CourseGroupUserRelation::PROPERTY_SUBSCRIPTION_TIME]
                );

            $table[$index]['Course Groups'] = $course_groups_string;
        }

        return $table;
    }

    /**
     * Retrieves the course groups when exporting all the course groups
     *
     * @param $worksheet <type>
     */
    protected function get_course_groups_tab($worksheet)
    {
        $courseGroupRoot = DataManager:: retrieve_course_group_root($this->get_course_id());
        $course_groups = $courseGroupRoot->get_children(false);

        $this->handle_course_groups($course_groups, $worksheet);
    }

    /**
     * Handles a resultset of course groups and their children
     *
     * @param ResultSet $course_groups
     * @param $worksheet
     * @param int $rowcount
     *
     * @return string
     */
    protected function handle_course_groups(ResultSet $course_groups, $worksheet, $rowcount = 0)
    {
        while ($course_group = $course_groups->next_result())
        {
            $course_group_users = DataManager:: retrieve_course_group_users_with_subscription_time(
                $course_group->get_id(),
                null,
                null,
                null,
                array(
                    new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME)),
                    new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME))
                )
            );

            $users_table = $this->get_users_table($course_group_users);
            $title = $course_group->get_name();

            $rowcount = $this->render_table(
                $worksheet,
                $title,
                $course_group->get_description(),
                $users_table,
                $rowcount
            );

            $rowcount = $this->handle_course_groups($course_group->get_children(), $worksheet, $rowcount);
        }

        return $rowcount;
    }

    /**
     * Renders the worksheet
     *
     * @return string - the path to the rendered worksheet
     */
    public function render()
    {
        $excel = new PHPExcel();
        $worksheet = $excel->getSheet(0)->setTitle('Export');

        $this->get_data($worksheet);

        $objWriter = PHPExcel_IOFactory:: createWriter($excel, 'Excel2007');

        $temp_dir = Path:: getInstance()->getTemporaryPath() . 'excel/';

        if (!is_dir($temp_dir))
        {
            mkdir($temp_dir, 0777, true);
        }

        $filename = 'export_course_group_' . $this->get_course_id();

        if ($this->course_group)
        {
            $filename .= '_' . $this->course_group->get_id();
        }

        $filename .= '_' . time();

        $file_path = $temp_dir . $filename;

        $objWriter->save($file_path);

        return $file_path;
    }

    /**
     * Gets the data for the worksheet
     *
     * @param $worksheet Worksheet
     */
    public function get_data($worksheet)
    {
        if ($this->course_group)
        {
            // subscription overview for one course group
            $this->get_course_group_subscription($worksheet);
        }
        else
        {
            $this->get_course_groups_tab($worksheet);
        }
    }

    /**
     * Gets the course group subscriptions for the given course group
     *
     * @param $worksheet <type>
     */
    private function get_course_group_subscription($worksheet)
    {
        // $data = WeblcmsDataManager ::
        // retrieve_all_course_users($this->get_course_id(),
        // $this->get_condition(), null, null, null);
        $data = DataManager:: retrieve_course_group_users_with_subscription_time(
            $this->course_group->get_id(), null, null, null, array(
                new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME)),
                new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME))
            )
        );

        $table = $this->get_users_table($data);
        $title = Translation:: get('SubscriptionList');
        $rowcount = 0;
        $this->render_table($worksheet, $title, $this->course_group->get_description(), $table, $rowcount);
    }

    /**
     * Renders the data
     *
     * @param $worksheet Worksheet
     * @param $title String
     * @param $table String[]
     * @param $block_row Integer
     *
     * @return Integer
     */
    private function render_table($worksheet, $title, $description, $table, $block_row)
    {
        $column = 0;
        $column1 = 1;
        $column2 = 2;
        $color = PHPExcel_Style_Color :: COLOR_BLUE;

        $styleArray = array(
            'font' => array('underline' => PHPExcel_Style_Font :: UNDERLINE_SINGLE, 'color' => array('argb' => $color))
        );

        $block_row ++;
        $block_row ++;

        $worksheet->setCellValueByColumnAndRow($column, $block_row, $title);
        // $this->wrap_text($worksheet, $column, $block_row);
        $worksheet->mergeCells('A' . $block_row . ':G' . $block_row);
        $worksheet->getStyleByColumnAndRow($column, $block_row)->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment :: HORIZONTAL_CENTER
        );
        $worksheet->getStyleByColumnAndRow($column, $block_row)->getFont()->setBold(true);

        $block_row ++;

        if ($description)
        {
            $block_row ++;

            $worksheet->mergeCells('A' . $block_row . ':G' . $block_row);
            $worksheet->getStyleByColumnAndRow($column, $block_row)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment :: HORIZONTAL_CENTER
            );

            $worksheet->setCellValueByColumnAndRow($column, $block_row, $description);

            $block_row ++;
        }

        // moved this block outside the loop, since it has nothing to do with the data
        {
            $worksheet->getColumnDimension('A')->setWidth(20);
            $worksheet->getColumnDimension('B')->setWidth(30);
            $worksheet->getColumnDimension('C')->setWidth(30);
            $worksheet->getColumnDimension('D')->setWidth(30);
            $worksheet->getColumnDimension('E')->setWidth(50);
            $worksheet->getColumnDimension('F')->setWidth(50);
            $worksheet->getColumnDimension('G')->setWidth(50);

            $worksheet->getStyleByColumnAndRow($column, $block_row + 1)->applyFromArray($styleArray);
            $worksheet->getStyleByColumnAndRow($column1, $block_row + 1)->applyFromArray($styleArray);
            $worksheet->getStyleByColumnAndRow($column2, $block_row + 1)->applyFromArray($styleArray);
            $worksheet->getStyleByColumnAndRow($column2 + 1, $block_row + 1)->applyFromArray($styleArray);
            $worksheet->getStyleByColumnAndRow($column2 + 2, $block_row + 1)->applyFromArray($styleArray);
            $worksheet->getStyleByColumnAndRow($column2 + 3, $block_row + 1)->applyFromArray($styleArray);
            $worksheet->getStyleByColumnAndRow($column2 + 4, $block_row + 1)->applyFromArray($styleArray);
        }

        // $i = 0;

        foreach ($table as $entry)
        {
            // $i++;
            $block_row ++;
            $worksheet->setCellValueByColumnAndRow($column, $block_row, $entry[User :: PROPERTY_OFFICIAL_CODE]);
            $worksheet->setCellValueByColumnAndRow($column1, $block_row, $entry[User :: PROPERTY_USERNAME]);
            $worksheet->setCellValueByColumnAndRow($column2, $block_row, $entry[User :: PROPERTY_LASTNAME]);
            $worksheet->setCellValueByColumnAndRow($column2 + 1, $block_row, $entry[User :: PROPERTY_FIRSTNAME]);
            $worksheet->setCellValueByColumnAndRow($column2 + 2, $block_row, $entry[User :: PROPERTY_EMAIL]);
            $worksheet->setCellValueByColumnAndRow(
                $column2 + 3, $block_row, $entry[CourseGroupUserRelation::PROPERTY_SUBSCRIPTION_TIME]
            );
            $worksheet->setCellValueByColumnAndRow($column2 + 4, $block_row, $entry['Course Groups']);
            // if ($i == 1)
        }

        // $block_row++;
        return $block_row;
    }
}
