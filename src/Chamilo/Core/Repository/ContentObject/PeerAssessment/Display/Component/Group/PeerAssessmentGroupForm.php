<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component\Group;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component\ViewerComponent;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class PeerAssessmentGroupForm extends FormValidator
{
    const FORM_NAME = 'peer_assessment_group_form';
    const PARAM_NAME = 'name';
    const PARAM_DESCRIPTION = 'description';
    const PARAM_USER = 'user';
    const PARAM_GROUP = 'group';
    const FORM_TYPE_EDIT = 1;
    const FORM_TYPE_CREATE = 2;

    /**
     *
     * @var PeerAssessmentDisplayViewerComponent
     */
    private $viewer;

    private $group_id = null;

    private $enroll_errors = null;

    /**
     * Constructor
     * 
     * @param PeerAssessmentDisplayViewerComponent $viewer
     */
    function __construct(ViewerComponent $viewer, $group_id, $url, $type = self :: FORM_TYPE_CREATE)
    {
        $this->viewer = $viewer;
        $this->group_id = $group_id;
        
        parent::__construct(self::FORM_NAME, 'post', $url);
        
        $this->build_basic_form();
        
        if ($type == self::FORM_TYPE_CREATE)
        {
            $this->build_create_buttons();
        }
        else
        {
            $this->build_edit_buttons();
        }
    }

    private function build_create_buttons()
    {
        $this->addElement(
            'style_submit_button', 
            FormValidator::PARAM_SUBMIT, 
            Translation::get('Create', null, Utilities::COMMON_LIBRARIES));
    }

    private function build_edit_buttons()
    {
        $this->addElement(
            'style_submit_button', 
            FormValidator::PARAM_SUBMIT, 
            Translation::get('Edit', null, Utilities::COMMON_LIBRARIES));
    }

    private function build_basic_form()
    {
        $this->add_textfield(self::PARAM_NAME, Translation::get('Name', null, Utilities::COMMON_LIBRARIES));
        $this->add_html_editor(
            self::PARAM_DESCRIPTION, 
            Translation::get('Description', null, Utilities::COMMON_LIBRARIES));
        
        $current = array();
        // set defaults if there are any
        if (! is_null($this->group_id))
        {
            $group_users = $this->viewer->get_group_users($this->group_id);
            foreach ($group_users as $user)
            {
                $current[] = array(
                    'id' => self::PARAM_USER . '_' . $user->get_id(), 
                    'classes' => 'type type_user', 
                    'title' => $user->get_firstname() . ' ' . $user->get_lastname(), 
                    'description' => $user->get_username());
            }
        }
        $locale = array(
            'Display' => Translation::get('SelectGroupUsers', null, Utilities::COMMON_LIBRARIES), 
            'Searching' => Translation::get('Searching', null, Utilities::COMMON_LIBRARIES), 
            'NoResults' => Translation::get('NoResults', null, Utilities::COMMON_LIBRARIES), 
            'Error' => Translation::get('Error', null, Utilities::COMMON_LIBRARIES), 
            'load_elements' => true);
        
        $elem = $this->addElement(
            'user_group_finder', 
            Manager::PARAM_GROUP_USERS, 
            Translation::get('SubscribeUsers', null, Utilities::COMMON_LIBRARIES), 
            $this->viewer->get_group_feed_path(), 
            $locale, 
            $current, 
            array('load_elements' => true));
    }

    /**
     * updates memberships of peer assessment group
     */
    function update_group_memberships()
    {
        if (! is_null($this->group_id))
        {
            $group_users_arr = $this->viewer->get_group_users($this->group_id);
            $group_users = array();
            
            foreach ($group_users_arr as $user)
            {
                $group_users[$user->get_id()] = $user->get_id();
            }
            
            $values = $this->exportValue(Manager::PARAM_GROUP_USERS);
            
            foreach ($values as $type => $elements)
            {
                foreach ($elements as $id)
                {
                    if ($type == self::PARAM_USER)
                    {
                        // type = user
                        if (! in_array($id, $group_users))
                        {
                            if (! $this->viewer->user_is_enrolled_in_group($id))
                            {
                                $this->viewer->add_user_to_group($id, $this->group_id); // user can be enrolled in one
                                                                                            // PA-group/publication
                            }
                            else
                            {
                                $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                                    \Chamilo\Core\User\Storage\DataClass\User::class_name(), 
                                    $id);
                                $already_enrolled[] = $user->get_firstname() . ' ' . $user->get_lastname();
                            }
                        }
                        else
                        {
                            unset($group_users[$id]);
                        }
                    }
                    // type = group
                    elseif ($type == self::PARAM_GROUP)
                    {
                        $context_group_users = $this->viewer->get_context_group_users($id);
                        
                        foreach ($context_group_users as $user)
                        {
                            if (! in_array($user->get_id(), $group_users))
                            {
                                if (! $this->viewer->user_is_enrolled_in_group($user->get_id()))
                                {
                                    $this->viewer->add_user_to_group($user->get_id(), $this->group_id); // user can be
                                                                                                            // enrolled in
                                                                                                            // one
                                                                                                            // PA-group/publication
                                }
                                else
                                {
                                    $already_enrolled[] = $user->get_firstname() . ' ' . $user->get_lastname();
                                }
                            }
                            else
                            {
                                unset($group_users[$id]);
                            }
                        }
                    }
                }
            }
            
            // remove remaining users
            foreach ($group_users as $user_id)
            {
                $this->viewer->remove_user_from_group($user_id, $this->group_id);
            }
        }
        
        if (count($already_enrolled) > 0)
            $this->enroll_errors = implode(',', $already_enrolled) . ' ' . Translation::get('AlreadyEnrolled');
    }

    function get_enroll_errors()
    {
        return $this->enroll_errors;
    }

    function set_group_id($group_id)
    {
        $this->group_id = $group_id;
    }
}