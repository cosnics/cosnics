<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Form;

use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\Publication\Publisher\Form\BasePublicationForm;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application\calendar$PublicationForm
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationForm extends BasePublicationForm
{
    // Types
    const TYPE_SINGLE = 1;
    const TYPE_MULTI = 2;
    
    // Parameters
    const PARAM_SHARE = 'share_users_and_groups';
    const PARAM_SHARE_ELEMENTS = 'share_users_and_groups_elements';
    const PARAM_SHARE_OPTION = 'share_users_and_groups_option';

    /**
     * The content object that will be published$
     * 
     * @var ContentObject
     */
    private $content_object;

    /**
     * The publication that will be changed (when using this form to edit a publication)
     * 
     * @var Publication
     */
    private $publication;

    /**
     * The publication that will be changed (when using this form to edit a publication)
     * 
     * @var \core\user\User
     */
    private $form_user;

    /**
     *
     * @var int
     */
    private $form_type;

    /**
     *
     * @param int $form_type
     * @param ContentObject $content_object
     * @param User $form_user
     * @param string $action
     * @param array $selectedContentObjects
     */
    public function __construct($form_type, $content_object, User $form_user, $action, $selectedContentObjects = array())
    {
        parent::__construct('publish', 'post', $action);
        
        $this->form_type = $form_type;
        $this->content_object = $content_object;
        $this->form_user = $form_user;
        
        $this->setSelectedContentObjects($selectedContentObjects);
        
        switch ($this->form_type)
        {
            case self::TYPE_SINGLE :
                $this->build_single_form();
                break;
            case self::TYPE_MULTI :
                $this->build_multi_form();
                break;
        }
        
        $this->add_footer();
        $this->setDefaults();
    }

    /**
     * Sets the default values of the form.
     * By default the publication is for everybody who has access to the tool and
     * the publication will be available forever.
     */
    public function setDefaults()
    {
        $defaults = array();
        $defaults[self::PARAM_SHARE_OPTION] = 0;
        parent::setDefaults($defaults);
    }

    public function build_single_form()
    {
        $this->build_form();
    }

    public function build_multi_form()
    {
        $this->build_form();
        $this->addElement('hidden', 'ids', serialize($this->content_object));
    }

    /**
     * Builds the form by adding the necessary form elements.
     */
    public function build_form()
    {
        $this->addSelectedContentObjects($this->form_user);
        
        $shares = array();
        $attributes = array();
        $attributes['search_url'] = Path::getInstance()->getBasePath(true) .
             'index.php?go=XmlUserGroupFeed&application=Chamilo%5CCore%5CGroup%5CAjax';
        $locale = array();
        $locale['Display'] = Translation::get('ShareWith', null, Utilities::COMMON_LIBRARIES);
        $locale['Searching'] = Translation::get('Searching', null, Utilities::COMMON_LIBRARIES);
        $locale['NoResults'] = Translation::get('NoResults', null, Utilities::COMMON_LIBRARIES);
        $locale['Error'] = Translation::get('Error', null, Utilities::COMMON_LIBRARIES);
        $attributes['locale'] = $locale;
        $attributes['exclude'] = array('user_' . $this->form_user->get_id());
        $attributes['defaults'] = array();
        
        $this->add_receivers(
            self::PARAM_SHARE, 
            Translation::get('ShareWith', null, Utilities::COMMON_LIBRARIES), 
            $attributes, 
            'Nobody');
    }

    public function add_footer()
    {
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Publish', null, Utilities::COMMON_LIBRARIES), 
            null, 
            null, 
            'arrow-right');
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     *
     * @return boolean
     */
    public function create_content_object_publication()
    {
        $values = $this->exportValues();
        
        $users = $values[self::PARAM_SHARE_ELEMENTS]['user'];
        $groups = $values[self::PARAM_SHARE_ELEMENTS]['group'];
        
        $pub = new Publication();
        $pub->set_content_object_id($this->content_object->get_id());
        $pub->set_publisher($this->form_user->get_id());
        $pub->set_published(time());
        $pub->set_target_users($users);
        $pub->set_target_groups($groups);
        
        if ($pub->create())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @return boolean
     */
    public function create_content_object_publications()
    {
        $values = $this->exportValues();
        
        $ids = unserialize($values['ids']);
        
        $users = $values[self::PARAM_SHARE_ELEMENTS]['user'];
        $groups = $values[self::PARAM_SHARE_ELEMENTS]['group'];
        
        foreach ($ids as $id)
        {
            $pub = new Publication();
            $pub->set_content_object_id($id);
            $pub->set_publisher($this->form_user->get_id());
            $pub->set_published(time());
            $pub->set_target_users($users);
            $pub->set_target_groups($groups);
            
            if (! $pub->create())
            {
                return false;
            }
        }
        return true;
    }

    /**
     *
     * @param Publication $publication
     */
    public function set_publication(Publication $publication)
    {
        $this->publication = $publication;
        $this->addElement('hidden', 'pid');
        $this->addElement('hidden', 'action');
        $defaults['action'] = 'edit';
        $defaults['pid'] = $publication->get_id();
        
        $target_groups = $this->publication->get_target_groups();
        $target_users = $this->publication->get_target_users();
        
        $defaults[self::PARAM_SHARE_ELEMENTS] = array();
        
        foreach ($target_groups as $target_group)
        {
            $group = \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(Group::class_name(), $target_group);
            
            $selected_group = array();
            $selected_group['id'] = 'group_' . $group->get_id();
            $selected_group['classes'] = 'type type_group';
            $selected_group['title'] = $group->get_name();
            $selected_group['description'] = $group->get_name();
            
            $defaults[self::PARAM_SHARE_ELEMENTS][$selected_group['id']] = $selected_group;
        }
        
        foreach ($target_users as $target_user)
        {
            $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User::class_name(), 
                (int) $target_user);
            
            $selected_user = array();
            $selected_user['id'] = 'user_' . $user->get_id();
            $selected_user['classes'] = 'type type_user';
            $selected_user['title'] = $user->get_fullname();
            $selected_user['description'] = $user->get_username();
            
            $defaults[self::PARAM_SHARE_ELEMENTS][$selected_user['id']] = $selected_user;
        }
        
        if (count($defaults[self::PARAM_SHARE_ELEMENTS]) > 0)
        {
            $defaults[self::PARAM_SHARE_OPTION] = '1';
        }
        
        $active = $this->getElement(self::PARAM_SHARE_ELEMENTS);
        $active->_elements[0]->setValue(serialize($defaults[self::PARAM_SHARE_ELEMENTS]));
        
        parent::setDefaults($defaults);
    }

    /**
     *
     * @return \application\personal_calendar\Publication
     */
    public function update_calendar_event_publication()
    {
        $values = $this->exportValues();
        
        $users = $values[self::PARAM_SHARE_ELEMENTS]['user'];
        $groups = $values[self::PARAM_SHARE_ELEMENTS]['group'];
        
        $this->publication->set_target_users($users);
        $this->publication->set_target_groups($groups);
        $this->publication->update();
        return $this->publication;
    }
}
