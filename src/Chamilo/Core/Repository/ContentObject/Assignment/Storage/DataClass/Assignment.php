<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @package repository.content_object.assignment.php This class represents an assignment
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class Assignment extends ContentObject implements AttachmentSupport
{
    const PROPERTY_START_TIME = 'start_time';
    const PROPERTY_END_TIME = 'end_time';
    const PROPERTY_VISIBILITY_SUBMISSIONS = 'visibility_submissions';
    const PROPERTY_ALLOW_LATE_SUBMISSIONS = 'allow_late_submissions';
    const PROPERTY_VISIBILTY_FEEDBACK = 'visibility_feedback';
    const PROPERTY_AUTOMATIC_FEEDBACK_TEXT = 'automatic_feedback_text';
    const PROPERTY_AUTOMATIC_FEEDBACK_CO_IDS = 'automatic_feedback_co_ids';
    const PROPERTY_SELECT_ATTACHMENT = 'select_attachment';
    const PROPERTY_ALLOWED_TYPES = 'allowed_types';
    const VISIBILITY_FEEDBACK_AFTER_END_TIME = 0;
    const VISIBILITY_FEEDBACK_AFTER_SUBMISSION = 1;

    const PROPERTY_PAGE_TEMPLATE = 'page_template';
    const PROPERTY_LAST_ENTRY_AS_TEMPLATE = 'last_entry_as_template';

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public static function get_additional_property_names()
    {
        return array(
            self::PROPERTY_START_TIME,
            self::PROPERTY_END_TIME,
            self::PROPERTY_VISIBILITY_SUBMISSIONS,
            self::PROPERTY_ALLOW_LATE_SUBMISSIONS,
            self::PROPERTY_AUTOMATIC_FEEDBACK_TEXT,
            self::PROPERTY_VISIBILTY_FEEDBACK,
            self::PROPERTY_AUTOMATIC_FEEDBACK_CO_IDS,
            self::PROPERTY_ALLOWED_TYPES,
            self::PROPERTY_PAGE_TEMPLATE,
            self::PROPERTY_LAST_ENTRY_AS_TEMPLATE
        );
    }

    /**
     * @return string
     */
    public function getPageTemplate()
    {
        return $this->get_additional_property(self::PROPERTY_PAGE_TEMPLATE);
    }

    /**
     * @param string $pageTemplate
     *
     * @return $this
     */
    public function setPageTemplate(string $pageTemplate)
    {
        $this->set_additional_property(self::PROPERTY_PAGE_TEMPLATE, $pageTemplate);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasPageTemplate()
    {
        return !empty($this->getPageTemplate());
    }

    /**
     * @return Page|null
     * @throws \Exception
     */
    public function getInMemoryPageObjectFromTemplate(): ?Page
    {
        if(!$this->hasPageTemplate())
        {
            return null;
        }

        $currentDateTime = new \DateTime();
        $currentTime = $currentDateTime->format('d/m/y - H:i');

        $page = new Page();
        $page->set_title($this->get_title() . ' (' . $currentTime . ')');
        $page->set_description($this->getPageTemplate());

        return $page;
    }

    /**
     * @return bool
     */
    public function useLastEntryAsTemplate()
    {
        return (bool) $this->get_additional_property(self::PROPERTY_LAST_ENTRY_AS_TEMPLATE);
    }

    /**
     * @param bool $useLastEntryAsTemplate
     *
     * @return $this
     */
    public function setUseLastEntryAsTemplate(bool $useLastEntryAsTemplate)
    {
        $this->set_additional_property(self::PROPERTY_LAST_ENTRY_AS_TEMPLATE, $useLastEntryAsTemplate);

        return $this;
    }

    public function get_start_time()
    {
        return $this->get_additional_property(self::PROPERTY_START_TIME);
    }

    public function set_start_time($start_time)
    {
        $this->set_additional_property(self::PROPERTY_START_TIME, $start_time);
    }

    public function get_end_time()
    {
        return $this->get_additional_property(self::PROPERTY_END_TIME);
    }

    public function set_end_time($end_time)
    {
        $this->set_additional_property(self::PROPERTY_END_TIME, $end_time);
    }

    public function get_visibility_submissions()
    {
        return $this->get_additional_property(self::PROPERTY_VISIBILITY_SUBMISSIONS);
    }

    public function set_visibility_submissions($visibility_submissions)
    {
        $this->set_additional_property(self::PROPERTY_VISIBILITY_SUBMISSIONS, $visibility_submissions);
    }

    public function get_allow_late_submissions()
    {
        return $this->get_additional_property(self::PROPERTY_ALLOW_LATE_SUBMISSIONS);
    }

    public function set_allow_late_submissions($allow_late_submissions)
    {
        $this->set_additional_property(self::PROPERTY_ALLOW_LATE_SUBMISSIONS, $allow_late_submissions);
    }

    public function get_visibility_feedback()
    {
        return $this->get_additional_property(self::PROPERTY_VISIBILTY_FEEDBACK);
    }

    public function set_visibility_feedback($visibility_feedback)
    {
        $this->set_additional_property(self::PROPERTY_VISIBILTY_FEEDBACK, $visibility_feedback);
    }

    public function get_automatic_feedback_text()
    {
        return $this->get_additional_property(self::PROPERTY_AUTOMATIC_FEEDBACK_TEXT);
    }

    public function set_automatic_feedback_text($automatic_feedback_text)
    {
        $this->set_additional_property(self::PROPERTY_AUTOMATIC_FEEDBACK_TEXT, $automatic_feedback_text);
    }

    public function get_automatic_feedback_co_ids()
    {
        return $this->get_additional_property(self::PROPERTY_AUTOMATIC_FEEDBACK_CO_IDS);
    }

    public function set_automatic_feedback_co_ids($automatic_feedback_co_ids)
    {
        $this->set_additional_property(self::PROPERTY_AUTOMATIC_FEEDBACK_CO_IDS, $automatic_feedback_co_ids);
    }

    public function get_allowed_types()
    {
        return $this->get_additional_property(self::PROPERTY_ALLOWED_TYPES);
    }

    public function set_allowed_types($allowed_types)
    {
        $this->set_additional_property(self::PROPERTY_ALLOWED_TYPES, $allowed_types);
    }

    public function isAutomaticFeedbackVisible()
    {
        return $this->hasAutomaticFeedback() && $this->isAutomaticFeedbackCurrentlyVisible();
    }

    public function hasAutomaticFeedback()
    {
        return !empty($this->get_automatic_feedback_text()) || !empty($this->get_automatic_feedback_co_ids());
    }

    public function isAutomaticFeedbackVisibleAfterSubmission()
    {
        return $this->get_visibility_feedback() == self::VISIBILITY_FEEDBACK_AFTER_SUBMISSION;
    }

    public function isAutomaticFeedbackVisibleAfterEndTime()
    {
        return $this->get_visibility_feedback() == self::VISIBILITY_FEEDBACK_AFTER_END_TIME;
    }

    /**
     * @return bool
     */
    public function hasEndTimePassed()
    {
        return $this->get_end_time() <= time();
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        return $this->get_start_time() <= time();
    }

    /**
     * @return bool
     */
    public function isInProgress()
    {
        return $this->isStarted() && !$this->hasEndTimePassed();
    }

    /**
     * @return bool
     */
    public function canSubmit()
    {
        return $this->isInProgress() || ($this->hasEndTimePassed() && $this->get_allow_late_submissions());
    }

    protected function isAutomaticFeedbackCurrentlyVisible()
    {
        return $this->isAutomaticFeedbackVisibleAfterSubmission() ||
            ($this->isAutomaticFeedbackVisibleAfterEndTime() && $this->hasEndTimePassed());
    }

    public function is_attached_to_or_included_in($object_id)
    {
        if(parent::is_attached_to_or_included_in($object_id))
        {
            return true;
        }

        if ($this->isObjectPartOfAutomaticFeedback($object_id))
        {
            return true;
        }

        $attachments = $this->getAutomaticFeedbackObjects();
        foreach ($attachments as $attachment)
        {
            if ($attachment->is_attached_to_or_included_in($object_id))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $objectId
     *
     * @return bool
     */
    protected function isObjectPartOfAutomaticFeedback($objectId)
    {
        $automaticFeedbackContentObjectIds = explode(',', $this->get_automatic_feedback_co_ids());

        return in_array($objectId, $automaticFeedbackContentObjectIds);
    }

    /**
     * @return ContentObject[]
     */
    public function getAutomaticFeedbackObjects()
    {
        $automaticFeedbackContentObjectIds = $this->get_automatic_feedback_co_ids();
        $contentObjects = \Chamilo\Core\Repository\Storage\DataManager::retrieves(
            ContentObject::class_name(), new InCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                explode(',', $automaticFeedbackContentObjectIds)
            )
        )->as_array();

        return $contentObjects;
    }
}
