<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Class CourseTeam
 */
class CourseTeamRelation extends DataClass
{
    const PROPERTY_COURSE_ID = 'course_group_id';
    const PROPERTY_TEAM_ID = 'team_id';

    /**
     * @param array $extended_property_names
     *
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array()):array
    {
        $extended_property_names[] = self::PROPERTY_COURSE_ID;
        $extended_property_names[] = self::PROPERTY_TEAM_ID;

        return parent::get_default_property_names($extended_property_names);
    }

    /**
     * @return int
     */
    public function getCourseId():int
    {
        return $this->get_default_property(self::PROPERTY_COURSE_ID);
    }

    /**
     * @param int $courseId
     */
    public function setCourseId(int $courseId)
    {
        $this->set_default_property(self::PROPERTY_COURSE_ID, $courseId);
    }

    /**
     * @return string
     */
    public function getTeamId():string
    {
        return $this->get_default_property(self::PROPERTY_TEAM_ID);
    }

    /**
     * @param string $teamId
     */
    public function setTeamId(string $teamId)
    {
        $this->set_default_property(self::PROPERTY_TEAM_ID, $teamId);
    }

    /**
     * @return string
     */
    public static function get_table_name():string
    {
        return 'weblcms_course_team_relation';
    }

}