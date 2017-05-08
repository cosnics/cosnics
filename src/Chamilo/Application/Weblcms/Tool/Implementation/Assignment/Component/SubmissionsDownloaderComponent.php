<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionCourseGroupBrowser\SubmissionCourseGroupsBrowserTable;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionGroupsBrowser\SubmissionGroupsBrowserTable;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionUsersBrowser\SubmissionUsersBrowserTable;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmitterSubmissions\SubmitterGroupSubmissionsTable;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmitterSubmissions\SubmitterUserSubmissionsTable;
use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportController;
use Chamilo\Core\Repository\Common\Export\ExportParameters;
use Chamilo\Core\Repository\Common\Export\Zip\ZipContentObjectExport;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This component allows the download of submissions.
 * 
 * @author Bert De Clercq (Hogeschool Gent)
 * @author Anthony Hurst (Hogeschool Gent)
 */
class SubmissionsDownloaderComponent extends SubmissionsManager
{

    private $publication;

    public function run()
    {
        $this->define_class_variables();
        
        if (! $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication))
        {
            throw new NotAllowedException();
        }
        
        $target_ids = $this->getRequest()->get(self::PARAM_TARGET_ID);
        $table_name = Request::post('table_name');
        if ($table_name)
        {
            switch ($table_name)
            {
                case SubmissionUsersBrowserTable::DEFAULT_NAME :
                case SubmissionGroupsBrowserTable::DEFAULT_NAME :
                case SubmissionCourseGroupsBrowserTable::DEFAULT_NAME:
                    $this->download_submissions_by_target($target_ids);
                    break;
                case SubmitterUserSubmissionsTable::DEFAULT_NAME :
                case SubmitterGroupSubmissionsTable::DEFAULT_NAME :
                    $this->download_submissions_by_ids(Request::post($table_name . '_id'));
                    break;
            }
        }
        elseif ($this->get_object_id())
        {
            $this->download_attachment($this->get_object_id());
        }
        elseif ($this->get_submission_id())
        {
            $this->download_submission($this->get_submission_id());
        }
        elseif ($this->get_target_id())
        {
            $this->download_submissions_by_target($this->get_target_id(), $this->get_submitter_type());
        }
        else
        {
            $this->download_submissions_by_publication($this->get_publication_id());
        }
    }

    /**
     * Directly downloads a single submission with the given submission id.
     * 
     * @param $submission_id int
     */
    private function download_submission($submission_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_ID), 
            new StaticConditionVariable($submission_id));
        
        $submission_trackers = \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::get_data(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
            null, 
            $condition)->as_array();
        $submission_tracker = $submission_trackers[0];
        
        $submission_tracker->get_content_object()->send_as_download();
    }

    /**
     * Directly downloads an attachment with the given attachment id.
     * 
     * @param $attachment_id int
     */
    private function download_attachment($attachment_id)
    {
        $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(), 
            $attachment_id);
        
        $content_object->send_as_download();
    }

    /**
     * Downloads the submissions with the given ids as a zip file.
     * 
     * @param $submission_ids array
     */
    private function download_submissions_by_ids($submission_ids)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_ID), 
            $submission_ids);
        
        $submission_trackers = \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::get_data(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
            null, 
            $condition)->as_array();
        $this->download_zipped_submissions($submission_trackers);
    }

    /**
     * Downloads all the zippable submissions from the users with the given target ids.
     * When submitter type is used,
     * only give one target id and it will download only the submissions from that specific user.
     * 
     * @param $target_ids mixed
     * @param $submitter_type int
     */
    private function download_submissions_by_target($target_ids, $submitter_type = null)
    {
        if ($submitter_type)
        {
            $submission_trackers = $this->get_submission_trackers_by_submitter($submitter_type, $target_ids);
            $this->download_zipped_submissions($submission_trackers);
        }
        else
        {
            switch (Request::get(self::PARAM_TYPE))
            {
                case SubmittersBrowserComponent::TYPE_COURSE_GROUP :
                    $this->download_submissions_for_submitters(
                        $target_ids, 
                        \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP);
                    break;
                case SubmittersBrowserComponent::TYPE_GROUP :
                    $this->download_submissions_for_submitters(
                        $target_ids, 
                        \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP);
                    break;
                default :
                    if ($this->publication->get_content_object()->get_allow_group_submissions())
                    {
                        $this->download_submissions_for_submitters(
                            $target_ids, 
                            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP);
                    }
                    else
                    {
                        $this->download_submissions_for_submitters(
                            $target_ids, 
                            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER);
                    }
                    break;
            }
        }
    }

    /**
     * Downloads all the zippable submissions that belong the the submitters with the given submitters ids, and are of
     * the given submitter type.
     * 
     * @param $submitter_ids array
     * @param $submitter_type int
     */
    private function download_submissions_for_submitters($submitter_ids, $submitter_type)
    {
        $conditions = array();
        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_ID), 
            $submitter_ids);
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_TYPE), 
            new StaticConditionVariable($submitter_type));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_PUBLICATION_ID), 
            new StaticConditionVariable($this->get_publication_id()));
        $condition = new AndCondition($conditions);
        
        $submission_trackers = \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::get_data(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
            null, 
            $condition)->as_array();
        $this->download_zipped_submissions($submission_trackers);
    }

    /**
     * Downloads all zippable submissions from a publication as a zip file.
     * 
     * @param $publication_id int
     */
    private function download_submissions_by_publication($publication_id)
    {
        $submission_trackers = $this->get_submission_trackers_by_publication($publication_id);
        
        $this->download_zipped_submissions($submission_trackers);
    }

    /**
     * Checks whether the submissions are zippable and then downloads them as a zip file.
     * 
     * @param $submission_trackers array
     */
    private function download_zipped_submissions($submission_trackers)
    {
        $content_object_ids = array();
        foreach ($submission_trackers as $submission_tracker)
        {
            if ($this->is_zippable($submission_tracker->get_content_object()))
            {
                $content_object_ids[] = $submission_tracker->get_content_object_id();
            }
        }
        
        if (count($content_object_ids) == 0)
        {
            if ($this->get_target_id())
            {
                
                $this->redirect(
                    Translation::get('DownloadNotPossible'), 
                    true, 
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_BROWSE_SUBMISSIONS, 
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->publication->get_id(), 
                        self::PARAM_TARGET_ID => $this->get_target_id(), 
                        self::PARAM_SUBMITTER_TYPE => $this->get_submitter_type()));
            }
            else
            {
                $this->redirect(
                    Translation::get('DownloadNotPossible'), 
                    true, 
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_BROWSE_SUBMITTERS, 
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->publication->get_id()));
            }
        }
        $parameters = new ExportParameters(
            new PersonalWorkspace($this->get_user()), 
            $this->get_user_id(), 
            ContentObjectExport::FORMAT_ZIP, 
            $content_object_ids, 
            array(), 
            ZipContentObjectExport::TYPE_FLAT);
        $exporter = ContentObjectExportController::factory($parameters);
        $exporter->download();
    }

    /**
     * Returns true if the given content object is zippable, and false otherwise.
     * Only content objects of the type
     * document are considered zippable for the moment.
     * 
     * @param $content_object ContentObject
     *
     * @return boolean True if the content object is zippable
     */
    private function is_zippable($content_object)
    {
        if (self::is_document($content_object))
        {
            return true;
        }
        
        return false;
    }

    /**
     * Defines the class variables.
     */
    public function define_class_variables()
    {
        $this->publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $this->get_publication_id());

        if (!$this->publication instanceof ContentObjectPublication)
        {
            throw new ObjectNotExistException(
                Translation::getInstance()->getTranslation(
                    'ContentObjectPublication', null, 'Chamilo\Application\Weblcms'
                ), $this->get_publication_id()
            );
        }
    }
}
