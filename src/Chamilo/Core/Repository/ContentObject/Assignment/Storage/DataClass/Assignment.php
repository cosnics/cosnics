<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
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
    const PROPERTY_ALLOWED_TYPES = 'allowed_types';
    const PROPERTY_ALLOW_LATE_SUBMISSIONS = 'allow_late_submissions';
    const PROPERTY_AUTOMATIC_FEEDBACK_CO_IDS = 'automatic_feedback_co_ids';
    const PROPERTY_AUTOMATIC_FEEDBACK_TEXT = 'automatic_feedback_text';
    const PROPERTY_END_TIME = 'end_time';
    const PROPERTY_SELECT_ATTACHMENT = 'select_attachment';
    const PROPERTY_START_TIME = 'start_time';
    const PROPERTY_VISIBILITY_SUBMISSIONS = 'visibility_submissions';
    const PROPERTY_VISIBILTY_FEEDBACK = 'visibility_feedback';

    const VISIBILITY_FEEDBACK_AFTER_END_TIME = 0;
    const VISIBILITY_FEEDBACK_AFTER_SUBMISSION = 1;

    /**
     * @return bool
     */
    public function canSubmit()
    {
        return $this->isInProgress() || ($this->hasEndTimePassed() && $this->get_allow_late_submissions());
    }

    /**
     * @return ContentObject[]
     */
    public function getAutomaticFeedbackObjects()
    {
        $automaticFeedbackContentObjectIds = $this->get_automatic_feedback_co_ids();
        $contentObjects = DataManager::retrieves(
            ContentObject::class, new InCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                explode(',', $automaticFeedbackContentObjectIds)
            )
        );

        return $contentObjects;
    }

    public static function getAdditionalPropertyNames(): array
    {
        return array(
            self::PROPERTY_START_TIME,
            self::PROPERTY_END_TIME,
            self::PROPERTY_VISIBILITY_SUBMISSIONS,
            self::PROPERTY_ALLOW_LATE_SUBMISSIONS,
            self::PROPERTY_AUTOMATIC_FEEDBACK_TEXT,
            self::PROPERTY_VISIBILTY_FEEDBACK,
            self::PROPERTY_AUTOMATIC_FEEDBACK_CO_IDS,
            self::PROPERTY_ALLOWED_TYPES
        );
    }

    public function get_allow_late_submissions()
    {
        return $this->getAdditionalProperty(self::PROPERTY_ALLOW_LATE_SUBMISSIONS);
    }

    public function get_allowed_types()
    {
        return $this->getAdditionalProperty(self::PROPERTY_ALLOWED_TYPES);
    }

    public function get_automatic_feedback_co_ids()
    {
        return $this->getAdditionalProperty(self::PROPERTY_AUTOMATIC_FEEDBACK_CO_IDS);
    }

    public function get_automatic_feedback_text()
    {
        return $this->getAdditionalProperty(self::PROPERTY_AUTOMATIC_FEEDBACK_TEXT);
    }

    public function get_end_time()
    {
        return $this->getAdditionalProperty(self::PROPERTY_END_TIME);
    }

    public function get_start_time()
    {
        return $this->getAdditionalProperty(self::PROPERTY_START_TIME);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'repository_assignment';
    }

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class, true);
    }

    public function get_visibility_feedback()
    {
        return $this->getAdditionalProperty(self::PROPERTY_VISIBILTY_FEEDBACK);
    }

    public function get_visibility_submissions()
    {
        return $this->getAdditionalProperty(self::PROPERTY_VISIBILITY_SUBMISSIONS);
    }

    public function hasAutomaticFeedback()
    {
        return !empty($this->get_automatic_feedback_text()) || !empty($this->get_automatic_feedback_co_ids());
    }

    /**
     * @return bool
     */
    public function hasEndTimePassed()
    {
        return $this->get_end_time() <= time();
    }

    protected function isAutomaticFeedbackCurrentlyVisible()
    {
        return $this->isAutomaticFeedbackVisibleAfterSubmission() ||
            ($this->isAutomaticFeedbackVisibleAfterEndTime() && $this->hasEndTimePassed());
    }

    public function isAutomaticFeedbackVisible()
    {
        return $this->hasAutomaticFeedback() && $this->isAutomaticFeedbackCurrentlyVisible();
    }

    public function isAutomaticFeedbackVisibleAfterEndTime()
    {
        return $this->get_visibility_feedback() == self::VISIBILITY_FEEDBACK_AFTER_END_TIME;
    }

    public function isAutomaticFeedbackVisibleAfterSubmission()
    {
        return $this->get_visibility_feedback() == self::VISIBILITY_FEEDBACK_AFTER_SUBMISSION;
    }

    /**
     * @return bool
     */
    public function isInProgress()
    {
        return $this->isStarted() && !$this->hasEndTimePassed();
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
     * @return bool
     */
    public function isStarted()
    {
        return $this->get_start_time() <= time();
    }

    public function is_attached_to_or_included_in($object_id)
    {
        if (parent::is_attached_to_or_included_in($object_id))
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

    public function set_allow_late_submissions($allow_late_submissions)
    {
        $this->setAdditionalProperty(self::PROPERTY_ALLOW_LATE_SUBMISSIONS, $allow_late_submissions);
    }

    public function set_allowed_types($allowed_types)
    {
        $this->setAdditionalProperty(self::PROPERTY_ALLOWED_TYPES, $allowed_types);
    }

    public function set_automatic_feedback_co_ids($automatic_feedback_co_ids)
    {
        $this->setAdditionalProperty(self::PROPERTY_AUTOMATIC_FEEDBACK_CO_IDS, $automatic_feedback_co_ids);
    }

    public function set_automatic_feedback_text($automatic_feedback_text)
    {
        $this->setAdditionalProperty(self::PROPERTY_AUTOMATIC_FEEDBACK_TEXT, $automatic_feedback_text);
    }

    public function set_end_time($end_time)
    {
        $this->setAdditionalProperty(self::PROPERTY_END_TIME, $end_time);
    }

    public function set_start_time($start_time)
    {
        $this->setAdditionalProperty(self::PROPERTY_START_TIME, $start_time);
    }

    public function set_visibility_feedback($visibility_feedback)
    {
        $this->setAdditionalProperty(self::PROPERTY_VISIBILTY_FEEDBACK, $visibility_feedback);
    }

    public function set_visibility_submissions($visibility_submissions)
    {
        $this->setAdditionalProperty(self::PROPERTY_VISIBILITY_SUBMISSIONS, $visibility_submissions);
    }
}
