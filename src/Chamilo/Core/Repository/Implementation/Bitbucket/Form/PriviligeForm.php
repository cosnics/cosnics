<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\Form;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class PriviligeForm extends FormValidator
{

    /**
     * The renderer used to display the form
     */
    private $renderer;

    private $bitbucket;
    const TYPE_PRIVILEGE = 'type';
    const TYPE_READ = 'read';
    const TYPE_WRITE = 'write';
    const TYPE_ADMIN = 'admin';

    public function __construct($action, $bitbucket)
    {
        parent :: __construct(ClassnameUtilities :: getInstance()->getClassnameFromObject($this, true), 'post', $action);
        $this->renderer = clone $this->defaultRenderer();

        $this->bitbucket = $bitbucket;
        $this->build();

        $this->accept($this->renderer);
    }

    public function build()
    {
        $this->renderer->setElementTemplate(
            '<div style="vertical-align: middle; float: left; margin-right: 5px;">{element}</div>');
        $this->addElement('text', 'username', Translation :: get('User'));
        $groups = self :: get_groups_name();
        if (count($groups) > 0)
        {
            $this->addElement('select', 'groups', Translation :: get('Groups'), $groups);
        }
        $this->addElement(
            'select',
            self :: TYPE_PRIVILEGE,
            Translation :: get('PrivilegeType'),
            self :: get_privileges_types());
        $this->addElement(
            'style_submit_button',
            'submit',
            Translation :: get('Grant', null, Utilities :: COMMON_LIBRARIES),
            null,
            null,
            'arrow-right');

        $this->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository\Implementation\Bitbucket', true) .
                     'PrivilegeGrantingForm.js'));
    }

    public function get_groups_name()
    {
        $username = Setting :: get('username', $this->bitbucket->get_external_repository()->get_id());
        $groups_name = $this->bitbucket->get_external_repository_manager_connector()->retrieve_groups('chamilo');
        foreach ($groups_name as $group_name)
        {
            $groups[$group_name->get_owner_username() . '/' . $group_name->get_slug()] = $group_name->get_slug();
        }
        return $groups;
    }

    /**
     * Display the form
     */
    public function toHtml()
    {
        $html = array();
        $html[] = '<div>';
        $html[] = $this->renderer->toHTML();
        $html[] = '</div>';
        return implode('', $html);
    }

    public function grant_privilege()
    {
        $values = $this->exportValues();
        $group = $values['groups'];
        $user = $values['username'];

        if ($user)
        {
            return $this->bitbucket->get_external_repository_manager_connector()->grant_user_privilege(
                $this->bitbucket->get_repository()->get_id(),
                $values['username'],
                $values['type']);
        }
        elseif ($group)
        {
            return $this->bitbucket->get_external_repository_manager_connector()->grant_group_privileges(
                $this->bitbucket->get_repository()->get_id(),
                $values['groups'],
                $values['type']);
        }
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
