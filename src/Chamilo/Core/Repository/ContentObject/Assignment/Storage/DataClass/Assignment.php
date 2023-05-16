<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass
 * @author  Joris Willems <joris.willems@gmail.com>
 * @author  Alexander Van Paemel
 */
class Assignment extends ContentObject implements AttachmentSupport
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\Assignment';

    public const PROPERTY_ALLOWED_TYPES = 'allowed_types';
    public const PROPERTY_ALLOW_LATE_SUBMISSIONS = 'allow_late_submissions';
    public const PROPERTY_AUTOMATIC_FEEDBACK_CO_IDS = 'automatic_feedback_co_ids';
    public const PROPERTY_AUTOMATIC_FEEDBACK_TEXT = 'automatic_feedback_text';
    public const PROPERTY_END_TIME = 'end_time';
    public const PROPERTY_SELECT_ATTACHMENT = 'select_attachment';
    public const PROPERTY_START_TIME = 'start_time';
    public const PROPERTY_VISIBILITY_SUBMISSIONS = 'visibility_submissions';
    public const PROPERTY_VISIBILTY_FEEDBACK = 'visibility_feedback';

    public const VISIBILITY_FEEDBACK_AFTER_END_TIME = 0;
    public const VISIBILITY_FEEDBACK_AFTER_SUBMISSION = 1;

    /**
     * @return bool
     */
    public function canSubmit()
    {
        return $this->isInProgress() || ($this->hasEndTimePassed() && $this->get_allow_late_submissions());
    }

    public static function getAdditionalPropertyNames(): array
    {
        return [
            self::PROPERTY_START_TIME,
            self::PROPERTY_END_TIME,
            self::PROPERTY_VISIBILITY_SUBMISSIONS,
            self::PROPERTY_ALLOW_LATE_SUBMISSIONS,
            self::PROPERTY_AUTOMATIC_FEEDBACK_TEXT,
            self::PROPERTY_VISIBILTY_FEEDBACK,
            self::PROPERTY_AUTOMATIC_FEEDBACK_CO_IDS,
            self::PROPERTY_ALLOWED_TYPES
        ];
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

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_assignment';
    }

    public function get_allow_late_submissions()
    {
        return $this->getAdditionalProperty(self::PROPERTY_ALLOW_LATE_SUBMISSIONS);
    }

    public function get_allowed_types(): array
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
