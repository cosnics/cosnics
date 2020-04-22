<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package application.lib.weblcms
 */

/**
 * This class represents a learning object publication feedback.
 * When publishing a learning object from the repository in the weblcms
 * application, attached to another learning object, a new object of this type
 * is created.
 */
class ContentObjectPublicationFeedback extends ContentObjectPublication
{

    /**
     * Constructor
     *
     * @param $id int The id of this learning object publiction
     * @param $learningObject ContentObject The learning object which is
     *        published by this publication
     * @param $course string The course code of the course where this
     *        publication is made
     * @param $tool string The tool where this publication is made
     * @param $parent_id int The id of this learning object publication parent
     * @param $category int The id of the learning object publication category
     *        in which this publication is stored
     * @param $targetUsers array The users for which this publication is made.
     *        If this array contains no elements, the publication is for
     *        everybody.
     * @param $targetCourseGroups array The course_groups for which this publication is made.
     *        If this array contains no elements, the publication is for
     *        everybody.
     * @param $fromDate int The date on which this publication should become
     *        available. If value is 0, publication is available forever.
     * @param $toDate int The date on which this publication should become
     *        unavailable. If value is 0, publication is available forever.
     * @param $repository_viewer int The user id of the person who created this
     *        publication.
     * @param $publicationDate int The date on which this publication was made.
     * @param $modifiedDate int The date on which this publication was updated.
     * @param $hidden boolean If true, this publication is invisible
     * @param $displayOrder int The display order of this publication in its
     *        location (course - tool - category)
     */
    public function __construct($id, $learningObject, $course, $tool, $parent_id, $repository_viewer, $publicationDate,
        $modifiedDate, $hidden, $emailSent)
    {
        parent::__construct(
            $id,
            $learningObject,
            $course,
            $tool,
            0,
            array(),
            array(),
            0,
            0,
            $repository_viewer,
            $publicationDate,
            $modifiedDate,
            $hidden,
            0,
            $emailSent);
        $this->set_parent_id($parent_id);
        $this->set_modified_date(time());
        $this->set_email_sent();
    }

    /*
     * Sets a property of this learning object publication. See constructor for detailed information about the property.
     * @see ContentObjectPublicationFeedback()
     */
    public function set_category_id($category)
    {
        parent::set_category(0);
    }

    public function set_target_users($targetUsers)
    {
        parent::set_target_users(array());
    }

    public function set_target_course_groups($targetCourseGroups)
    {
        parent::set_target_course_groups(array());
    }

    public function set_from_date($fromDate)
    {
        parent::set_from_date(0);
    }

    public function set_to_date($toDate)
    {
        parent::set_to_date(0);
    }

    public function set_hidden($hidden)
    {
        parent::set_hidden(0);
    }

    public function set_display_order_index($displayOrder)
    {
        parent::set_display_order_index(0);
    }

    public function set_email_sent($emailSent)
    {
        parent::set_email_sent(0);
    }

    public function create()
    {
        if (Request::get(Manager::PARAM_PUBLICATION_ID))
        {
            $this->update_parent_modified_date();
        }
        return parent::create();
    }

    public function update()
    {
        $this->update_parent_modified_date();
        return parent::update();
    }

    public function update_parent_modified_date()
    {
        $parent_object = DataManager::retrieve_by_id(ContentObjectPublication::class_name(), $this->get_parent_id());
        $parent_object->set_modified_date(time());
        $parent_object->update();
    }
}
