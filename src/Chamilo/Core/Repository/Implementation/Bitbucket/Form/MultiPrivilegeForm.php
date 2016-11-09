<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\Form;

use Chamilo\Core\Repository\Implementation\Bitbucket\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class MultiPrivilegeForm extends FormValidator
{
    const TYPES_PRIVILEGES = 'types';
    const GROUPS = 'groups';
    const TYPE_READ = 'read';
    const TYPE_WRITE = 'write';
    const TYPE_ADMIN = 'admin';

    public function __construct($component)
    {
        parent :: __construct(
            ClassnameUtilities :: getInstance()->getClassnameFromObject($this, true),
            'post',
            $component->get_url());
        $this->component = $component;

        $this->build();
    }

    public function build()
    {
        $this->addElement('text', 'username', Translation :: get('User'));
        $this->addElement('select', self :: GROUPS, Translation :: get('Groups'), self :: get_groups_name());
        $this->addElement(
            'select',
            self :: TYPES_PRIVILEGES,
            Translation :: get('Privilege'),
            self :: get_privileges_types());

        $url = $this->component->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_RENDER_REPOSITORY_FEED));
        $locale = array();
        $locale['Display'] = Translation :: get('AddAttachments');
        $locale['Searching'] = Translation :: get('Searching', null, Utilities :: COMMON_LIBRARIES);
        $locale['NoResults'] = Translation :: get('NoResults', null, Utilities :: COMMON_LIBRARIES);
        $locale['Error'] = Translation :: get('Error', null, Utilities :: COMMON_LIBRARIES);

        $options = array('load_elements' => true);

        $element_finder = $this->addElement(
            'element_finder',
            'repositories',
            Translation :: get('SelectAttachment'),
            $url,
            $locale,
            array(),
            $options);

        $this->addElement(
            'style_submit_button',
            'submit',
            Translation :: get('Grant', null, Utilities :: COMMON_LIBRARIES),
            null,
            null,
            'arrow-right');

        $this->addElement(
            'html',
            ResourceManager :: getInstance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository\Implementation\Bitbucket', true) .
                     'PrivilegeGrantingForm.js'));
    }

    public function grant_privilege()
    {
        $values = $this->exportValues();
        $username = Setting :: get('username', $this->component->get_external_repository()->get_id());
        $user = $values['username'];
        $group = $values['groups'];

        foreach ($values[repositories][repository] as $repository)
        {
            if ($user)
            {
                $success = $this->component->get_external_repository_manager_connector()->grant_user_privilege(
                    $username . '/' . $repository,
                    $user,
                    $values['types']);
            }
            else
            {
                if ($group)
                {
                    $success = $this->component->get_external_repository_manager_connector()->grant_group_privileges(
                        $username . '/' . $repository,
                        $group,
                        $values['types']);
                }
            }
            if (! $success)
            {
                return false;
            }
        }
        return true;
    }

    public function get_groups_name()
    {
        $username = Setting :: get('username', $this->component->get_external_repository()->get_id());
        $groups_name = $this->component->get_external_repository_manager_connector()->retrieve_groups($username);
        foreach ($groups_name as $group_name)
        {
            $groups[$group_name->get_owner_username() . '/' . $group_name->get_slug()] = $group_name->get_slug();
        }
        return $groups;
    }

    public static function get_privileges_types()
    {
        $privileges_types = array();
        $privileges_types[self :: TYPE_READ] = Translation :: get('Read');
        $privileges_types[self :: TYPE_WRITE] = Translation :: get('Write');
        $privileges_types[self :: TYPE_ADMIN] = Translation :: get('Admin');

        return $privileges_types;
    }
}
