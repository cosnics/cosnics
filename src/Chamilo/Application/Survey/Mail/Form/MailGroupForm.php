<?php
namespace Chamilo\Application\Survey\Mail\Form;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;

class MailGroupForm extends FormValidator
{
    const APPLICATION_NAME = 'survey';
    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
    const PARAM_RIGHTS = 'rights';

    private $parent;

    private $publication;

    private $user;

    function __construct($action, $user)
    {
        parent :: __construct('select_mail_group', 'post', $action);

        $this->user = $user;
        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $publication = $this->publication;

        $attributes = array();
        $attributes['search_url'] = Path :: getInstance()->getBasePath(true) . 'group/php/xml_feeds/xml_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('ShareWith');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['defaults'] = array();
        $attributes['options'] = array('load_elements' => false);

        $this->add_receivers(
            self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET,
            Translation :: get('SubscribeGroups'),
            $attributes);

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('AddGroups'),
            null,
            null,
            'arrow-right');
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $this->addElement('category');
        $this->addElement('html', '<br />');
        $defaults[self :: APPLICATION_NAME . '_opt_forever'] = 1;
        $defaults[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_OPTION] = 0;
        $this->setDefaults($defaults);
    }

    function get_seleted_group_user_ids()
    {

        // $publication_id = $this->publication->get_id();
        $values = $this->exportValues();

        $user_ids = array();

        if ($values[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_OPTION] == 0)
        {
            // all users of the system will be checked
            $users = \Chamilo\Core\User\Storage\DataManager :: retrieve_users();

            while ($user = $users->next_result())
            {
                $user_ids[] = $user->get_id();
            }
        }
        else
        {
            $group_ids = $values[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET . '_elements']['group'];

            if (count($group_ids))
            {
                foreach ($group_ids as $group_id)
                {
                    $group_user_ids = array();
                    foreach ($group_ids as $group_id)
                    {

                        $group = \Chamilo\Core\Group\Storage\DataManager :: retrieve_group($group_id);
                        $ids = $group->get_users(true, true);
                        $group_user_ids = array_merge($group_user_ids, $ids);
                    }
                    $user_ids = array_unique($group_user_ids);
                }
            }
        }

        return $user_ids;
    }
}

?>