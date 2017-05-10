<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use ErrorException;
// to support mailing CO publications:
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\FileLogger;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Mail\ValueObject\MailFile;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use DOMDocument;

/**
 * $Id: content_object_publication.class.php 218 2009-11-13 14:21:26Z kariboe $
 * 
 * @package application.lib.weblcms
 */

/**
 * This class represents a learning object publication.
 * When publishing a learning object from the repository in the
 * weblcms application, a new object of this type is created.
 */
class ContentObjectPublication extends \Chamilo\Core\Repository\Publication\Storage\DataClass\Publication implements 
    DisplayOrderDataClassListenerSupport
{
    
    /*
     * #@+ Constant defining a property of the publication
     */
    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_TOOL = 'tool';
    const PROPERTY_CATEGORY_ID = 'category_id';
    const PROPERTY_FROM_DATE = 'from_date';
    const PROPERTY_TO_DATE = 'to_date';
    const PROPERTY_HIDDEN = 'hidden';
    const PROPERTY_PUBLISHER_ID = 'publisher_id';
    const PROPERTY_PUBLICATION_DATE = 'published';
    const PROPERTY_MODIFIED_DATE = 'modified';
    const PROPERTY_DISPLAY_ORDER_INDEX = 'display_order';
    const PROPERTY_EMAIL_SENT = 'email_sent';
    const PROPERTY_SHOW_ON_HOMEPAGE = 'show_on_homepage';
    const PROPERTY_ALLOW_COLLABORATION = 'allow_collaboration';
    const TYPE_FILE = 'file';
    const CONTENT_OBJECT_MODIFICATION_DATE_ALIAS = 'content_object_modification_date';
    
    // added to support mailing within content object
    private $target_course_groups;

    private $target_users;

    private $target_groups;

    private $publisher;

    public function __construct($default_properties = array(), $optional_properties = array())
    {
        parent::__construct($default_properties, $optional_properties);
        $this->add_listener(new DisplayOrderDataClassListener($this));
    }

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_COURSE_ID, 
                self::PROPERTY_TOOL, 
                self::PROPERTY_CATEGORY_ID, 
                self::PROPERTY_FROM_DATE, 
                self::PROPERTY_TO_DATE, 
                self::PROPERTY_HIDDEN, 
                self::PROPERTY_PUBLISHER_ID, 
                self::PROPERTY_PUBLICATION_DATE, 
                self::PROPERTY_MODIFIED_DATE, 
                self::PROPERTY_DISPLAY_ORDER_INDEX, 
                self::PROPERTY_EMAIL_SENT, 
                self::PROPERTY_SHOW_ON_HOMEPAGE, 
                self::PROPERTY_ALLOW_COLLABORATION));
    }

    /**
     * Gets the course code of the course in which this publication was made.
     * 
     * @return string The course code
     */
    public function get_course_id()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_ID);
    }

    /**
     * Gets the tool in which this publication was made.
     * 
     * @return string
     */
    public function get_tool()
    {
        return $this->get_default_property(self::PROPERTY_TOOL);
    }

    /**
     * Gets the id of the learning object publication category in which this publication was made
     * 
     * @return int
     */
    public function get_category_id()
    {
        return $this->get_default_property(self::PROPERTY_CATEGORY_ID);
    }

    /**
     * Gets the list of target users of this publication
     * 
     * @return array An array of user ids.
     * @see is_for_everybody()
     */
    public function get_target_users()
    {
        if (! isset($this->target_users))
        {
            $this->target_users = DataManager::retrieve_publication_target_user_ids($this->get_id());
        }
        
        return $this->target_users;
    }

    /**
     * Gets the list of target course_groups of this publication
     * 
     * @return array An array of course_group ids.
     * @see is_for_everybody()
     */
    public function get_target_course_groups()
    {
        if (! isset($this->target_course_groups))
        {
            $this->target_course_groups = DataManager::retrieve_publication_target_course_group_ids($this->get_id());
        }
        
        return $this->target_course_groups;
    }

    /**
     * Gets the list of target groups of this publication
     * 
     * @return array An array of group ids.
     * @see is_for_everybody()
     */
    public function get_target_groups()
    {
        if (! isset($this->target_groups))
        {
            $this->target_groups = DataManager::retrieve_publication_target_platform_group_ids($this->get_id());
        }
        
        return $this->target_groups;
    }

    /**
     * Gets the date on which this publication becomes available
     * 
     * @return int
     * @see is_forever()
     */
    public function get_from_date()
    {
        return $this->get_default_property(self::PROPERTY_FROM_DATE);
    }

    /**
     * Gets the date on which this publication becomes unavailable
     * 
     * @return int
     * @see is_forever()
     */
    public function get_to_date()
    {
        return $this->get_default_property(self::PROPERTY_TO_DATE);
    }

    /**
     * Gets the user id of the user who made this publication
     * 
     * @return int
     */
    public function get_publisher_id()
    {
        return $this->get_default_property(self::PROPERTY_PUBLISHER_ID);
    }

    /**
     * Retrieves the content object associated with this publication.
     * 
     * @param bool $full Whether the content object must have its full complement of properties.
     */
    public function get_content_object($full = false)
    {
        if (is_null($this->contentObject) || $full)
        {
            if (! is_null($this->get_optional_property(ContentObject::PROPERTY_TITLE)) && ! $full)
            {
                $class = $this->get_optional_property(ContentObject::PROPERTY_TYPE);
                $this->contentObject = new $class($this->get_optional_properties());
                $this->contentObject->setId($this->get_content_object_id());
            }
            else
            {
                return parent::getContentObject();
            }
        }
        
        return $this->contentObject;
    }

    public function get_publication_publisher()
    {
        if (! isset($this->publisher))
        {
            $this->publisher = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User::clasS_name(), 
                $this->get_publisher_id());
        }
        
        return $this->publisher;
    }

    /**
     * Sets the publication publisher for caching
     * 
     * @param $user ; User
     */
    public function set_publication_publisher(User $user)
    {
        $this->publisher = $user;
    }

    /**
     * Gets the date on which this publication was made
     * 
     * @return int
     */
    public function get_publication_date()
    {
        return $this->get_default_property(self::PROPERTY_PUBLICATION_DATE);
    }

    /**
     * Gets the date on which this publication was made
     * 
     * @return int
     */
    public function get_modified_date()
    {
        return $this->get_default_property(self::PROPERTY_MODIFIED_DATE);
    }

    /**
     * Determines whether this publication was sent by email to the users and course_groups for which this publication
     * was made
     * 
     * @return boolean True if an email was sent
     */
    public function is_email_sent()
    {
        return $this->get_default_property(self::PROPERTY_EMAIL_SENT);
    }

    /**
     * Determines whether this publication is hidden or not
     * 
     * @return boolean True if the publication is hidden.
     */
    public function is_hidden()
    {
        return $this->get_default_property(self::PROPERTY_HIDDEN);
    }

    /**
     * Determines whether this publication is available forever
     * 
     * @return boolean True if the publication is available forever
     * @see get_from_date()
     * @see get_to_date()
     */
    public function is_forever()
    {
        return $this->get_from_date() == 0 && $this->get_to_date() == 0;
    }

    public function is_for_everybody()
    {
        return (count($this->get_target_users()) == 0 && count($this->get_target_course_groups()) == 0 &&
             count($this->get_target_groups()) == 0);
    }

    public function is_visible_for_target_users()
    {
        return (! $this->is_hidden()) &&
             ($this->is_forever() || ($this->get_from_date() <= time() && time() <= $this->get_to_date()));
    }

    public function get_display_order_index()
    {
        return $this->get_default_property(self::PROPERTY_DISPLAY_ORDER_INDEX);
    }

    public function set_course_id($course)
    {
        $this->set_default_property(self::PROPERTY_COURSE_ID, $course);
    }

    public function set_tool($tool)
    {
        $this->set_default_property(self::PROPERTY_TOOL, $tool);
    }

    public function set_category_id($category)
    {
        $this->set_default_property(self::PROPERTY_CATEGORY_ID, $category);
    }

    public function set_target_users($target_users)
    {
        $this->target_users = $target_users;
    }

    public function set_target_course_groups($target_course_groups)
    {
        $this->target_course_groups = $target_course_groups;
    }

    public function set_target_groups($target_groups)
    {
        $this->target_groups = $target_groups;
    }

    public function set_from_date($from_date)
    {
        $this->set_default_property(self::PROPERTY_FROM_DATE, $from_date);
    }

    public function set_to_date($to_date)
    {
        $this->set_default_property(self::PROPERTY_TO_DATE, $to_date);
    }

    public function set_publisher_id($publisher)
    {
        $this->set_default_property(self::PROPERTY_PUBLISHER_ID, $publisher);
    }

    public function set_publication_date($publication_date)
    {
        $this->set_default_property(self::PROPERTY_PUBLICATION_DATE, $publication_date);
    }

    public function set_modified_date($modified_date)
    {
        $this->set_default_property(self::PROPERTY_MODIFIED_DATE, $modified_date);
    }

    public function set_hidden($hidden)
    {
        $this->set_default_property(self::PROPERTY_HIDDEN, $hidden);
    }

    public function set_display_order_index($display_order)
    {
        $this->set_default_property(self::PROPERTY_DISPLAY_ORDER_INDEX, $display_order);
    }

    public function set_email_sent($email_sent)
    {
        $this->set_default_property(self::PROPERTY_EMAIL_SENT, $email_sent);
    }

    /*
     * #@-
     */
    
    /**
     * Toggles the visibility of this publication.
     */
    public function toggle_visibility()
    {
        $this->set_hidden((integer) ! $this->is_hidden());
    }

    public function get_show_on_homepage()
    {
        return $this->get_default_property(self::PROPERTY_SHOW_ON_HOMEPAGE);
    }

    public function set_show_on_homepage($show_on_homepage)
    {
        $this->set_default_property(self::PROPERTY_SHOW_ON_HOMEPAGE, $show_on_homepage);
    }

    public function get_allow_collaboration()
    {
        return $this->get_default_property(self::PROPERTY_ALLOW_COLLABORATION);
    }

    public function set_allow_collaboration($allow_collaboration)
    {
        $this->set_default_property(self::PROPERTY_ALLOW_COLLABORATION, $allow_collaboration);
    }

    /**
     * Creates this publication in persistent storage
     */
    public function create($create_in_batch = false)
    {
        if (is_null($this->get_category_id()))
        {
            $this->set_category_id(0);
        }
        
        if (! parent::create())
        {
            return false;
        }
        
        if ($this->get_category_id())
        {
            $parent = WeblcmsRights::getInstance()->get_weblcms_location_id_by_identifier_from_courses_subtree(
                WeblcmsRights::TYPE_COURSE_CATEGORY, 
                $this->get_category_id(), 
                $this->get_course_id());
        }
        else
        {
            if ($this->get_tool() == 'Home')
            {
                $parent_id = WeblcmsRights::getInstance()->get_courses_subtree_root_id($this->get_course_id());
                
                return WeblcmsRights::getInstance()->create_location_in_courses_subtree(
                    WeblcmsRights::TYPE_PUBLICATION, 
                    $this->get_id(), 
                    $parent_id, 
                    $this->get_course_id(), 
                    $create_in_batch);
            }
            else
            {
                $course_tool = DataManager::retrieve_course_tool_by_name($this->get_tool());
                $course_tool_id = $course_tool->get_id();
                
                $parent = WeblcmsRights::getInstance()->get_weblcms_location_id_by_identifier_from_courses_subtree(
                    WeblcmsRights::TYPE_COURSE_MODULE, 
                    $course_tool_id, 
                    $this->get_course_id());
            }
        }
        
        return WeblcmsRights::getInstance()->create_location_in_courses_subtree(
            WeblcmsRights::TYPE_PUBLICATION, 
            $this->get_id(), 
            $parent, 
            $this->get_course_id(), 
            $create_in_batch);
    }

    /**
     * Moves the publication up or down in the list.
     * 
     * @param $places The number of places to move the publication down. A negative number moves it up.
     * @return int The number of places that the publication was moved down.
     */
    public function move($places)
    {
        $this->set_display_order_index($this->get_display_order_index() + $places);
        $success = $this->update();
        
        return $success ? $places : 0;
    }

    public function delete()
    {
        $location = WeblcmsRights::getInstance()->get_weblcms_location_by_identifier_from_courses_subtree(
            WeblcmsRights::TYPE_PUBLICATION, 
            $this->get_id(), 
            $this->get_course_id());
        if ($location)
        {
            if (! $location->delete())
            {
                return false;
            }
        }
        
        if (! parent::delete())
        {
            return false;
        }
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Feedback::class_name(), Feedback::PROPERTY_PUBLICATION_ID), 
            new StaticConditionVariable($this->get_id()));
        
        return DataManager::deletes(Feedback::class_name(), $condition);
    }

    public function get_target_entities()
    {
        try
        {
            return WeblcmsRights::getInstance()->get_target_entities(
                WeblcmsRights::VIEW_RIGHT, 
                Manager::context(), 
                $this->get_id(), 
                WeblcmsRights::TYPE_PUBLICATION, 
                $this->get_course_id(), 
                WeblcmsRights::TREE_TYPE_COURSE);
        }
        catch (ErrorException $exception)
        {
            error_log($exception->getMessage());
            
            return false;
        }
    }

    public function render_target_entities_as_string()
    {
        return WeblcmsRights::getInstance()->render_target_entities_as_string($this->get_target_entities());
    }

    /**
     * Returns the property for the display order
     * 
     * @return string
     */
    public function get_display_order_property()
    {
        return new PropertyConditionVariable(self::class_name(), self::PROPERTY_DISPLAY_ORDER_INDEX);
    }

    /**
     * Returns the properties that define the context for the display order (the properties on which has to be limited)
     * 
     * @return Condition
     */
    public function get_display_order_context_properties()
    {
        return array(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_COURSE_ID), 
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_TOOL), 
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_CATEGORY_ID));
    }

    /**
     * Ignores the display order for this specific object
     */
    public function ignore_display_order()
    {
        $this->remove_listener(0);
    }

    /**
     * Mails the given publication to the target users Originally appeared in content_object_publication_form.class.php,
     * refactored to support mailing after publication was published.
     * 
     * @param $publication ContentObjectPublication
     */
    public function mail_publication($after_publication = false)
    {
        set_time_limit(3600);
        
        // prepare mail
        $user = $this->get_publication_publisher();
        $content_object = $this->get_content_object();
        $tool = $this->get_tool();
        $link = $this->get_course_viewer_link();
        $course = CourseDataManager::retrieve_course($this->get_course_id());
        
        $body = Translation::get('NewPublicationMailDescription') . ' ' . $course->get_title() . ' : <a href="' . $link .
             '" target="_blank">' . utf8_decode($content_object->get_title()) . '</a><br />--<br />';
        $body .= $content_object->get_description();
        $body .= '--<br />';
        $body .= $user->get_fullname() . ' - ' . $course->get_visual_code() . ' - ' . $course->get_title() . ' - ' .
             Translation::get('TypeName', null, 'Chamilo\Application\Weblcms\Tool\Implementation\\' . $tool);
        
        // get targets
        $target_email = array();
        
        // Add the publisher to the email address
        $target_email[] = $user->get_email();
        
        $target_users = DataManager::get_publication_target_users($this);
        
        foreach ($target_users as $target_user)
        {
            $target_email[] = $target_user[User::PROPERTY_EMAIL];
        }
        
        // safety check: filter any dubbles
        $unique_email = array_unique($target_email);
        
        $site_name = Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'site_name'));
        
        $doc = new DOMDocument();
        $doc->loadHTML($body);
        $elements = $doc->getElementsByTagname('resource');
        
        $mailFiles = array();
        $index = 0;
        
        // replace image document resource tags with a html img tag with base64
        // data
        // remove all other resource tags
        foreach ($elements as $i => $element)
        {
            $type = $element->attributes->getNamedItem('type')->value;
            $id = $element->attributes->getNamedItem('source')->value;
            if ($type == self::TYPE_FILE)
            {
                $object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(ContentObject::class_name(), $id);
                if ($object->is_image())
                {
                    $mailFiles[] = new MailFile(
                        $object->get_filename(), 
                        $object->get_full_path(), 
                        $object->get_mime_type());
                    
                    $elem = $doc->createElement('img');
                    $elem->setAttribute('src', 'cid:' . $index);
                    $elem->setAttribute('alt', $object->get_filename());
                    $element->parentNode->replaceChild($elem, $element);
                    
                    $index ++;
                }
                else
                {
                    $element->parentNode->removeChild($element);
                }
            }
            else
            {
                $element->parentNode->removeChild($element);
            }
        }
        
        $body = $doc->saveHTML();
        
        if ($content_object->has_attachments())
        {
            $body .= '<br ><br >' . Translation::get('AttachmentWarning', array('LINK' => $link));
        }
        
        $log = '';
        $log .= "mail for publication " . $this->get_id() . " in course ";
        $log .= $course->get_title();
        $log .= " to: \n";
        
        $subject = Translation::get(
            'NewPublicationMailSubject', 
            array('COURSE' => $course->get_title(), 'CONTENTOBJECT' => $content_object->get_title()));
        
        $mail = new Mail(
            $subject, 
            $body, 
            $unique_email, 
            true, 
            array(), 
            array(), 
            $user->get_fullname(), 
            $user->get_email(), 
            null, 
            null, 
            $mailFiles);
        
        $mailerFactory = new MailerFactory(Configuration::getInstance());
        $mailer = $mailerFactory->getActiveMailer();
        
        try
        {
            $mailer->sendMail($mail);
            
            $log .= " (successfull)\n";
        }
        catch (\Exception $ex)
        {
            $log .= " (unsuccessfull)\n";
        }
        
        $logMails = Configuration::getInstance()->get_setting(array('Chamilo\Application\Weblcms', 'log_mails'));
        
        if ($logMails)
        {
            $dir = Path::getInstance()->getLogPath() . 'mail';
            
            if (! file_exists($dir) and ! is_dir($dir))
            {
                mkdir($dir);
            }
            
            $today = date("Ymd", mktime());
            $logfile = $dir . '//' . "mails_sent_$today" . ".log";
            $mail_log = new FileLogger($logfile, true);
            $mail_log->log_message($log, true);
        }
        
        $this->set_email_sent(true);
        $this->update();
    }

    private function get_course_viewer_link()
    {
        $params = array();
        
        $params[Manager::PARAM_CONTEXT] = Manager::package();
        $params[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_COURSE;
        $params[Manager::PARAM_COURSE] = $this->get_course_id();
        $params[Manager::PARAM_TOOL] = $this->get_tool();
        $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW;
        $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID] = $this->getId();
        
        $redirect = new Redirect($params);
        
        return $redirect->getUrl();
    }
}
