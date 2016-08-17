<?php

/**
 * QuickForm rule to if a course type is selected
 * 
 * @author Sven Vanpoucke
 */
class HTML_QuickForm_Rule_Course_Type extends HTML_QuickForm_Rule
{

    /**
     * Checks whether or not a given course type id is not -1 (the select course type value)
     * 
     * @param int $course_type_id
     *
     * @return boolean
     */
    public function validate($course_type_id)
    {
        return $course_type_id != - 1;
    }
}
