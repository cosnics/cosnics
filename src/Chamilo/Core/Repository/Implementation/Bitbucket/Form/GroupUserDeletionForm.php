<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\Form;

use Chamilo\Core\Repository\Implementation\Bitbucket\Manager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class GroupUserDeletionForm extends FormValidator
{

    /**
     * The renderer used to display the form
     */
    private $bitbucket;
    const USERS = 'user';

    public function __construct($action, $bitbucket)
    {
        parent :: __construct(ClassnameUtilities :: getInstance()->getClassnameFromObject($this, true), 'post', $action);

        $this->bitbucket = $bitbucket;
        $this->build();
    }

    public function build()
    {
        $this->addElement('select', self :: USERS, Translation :: get('Users'), self :: get_users());

        $this->addElement(
            'style_submit_button',
            'submit',
            Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
            null,
            null,
            'arrow-right');

        $this->addElement(
            'html',
            ResourceManager :: getInstance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository\Implementation\Bitbucket', true) .
                     'PrivilegeGrantingForm.js'));
    }

    public function delete_user_from_group()
    {
        $values = $this->exportValues();
        return $this->bitbucket->get_external_repository_manager_connector()->delete_user_from_group(
            Request :: get(Manager :: PARAM_EXTERNAL_REPOSITORY_GROUP),
            $values['user']);
    }

    public function get_users()
    {
        $members = $this->bitbucket->get_external_repository_manager_connector()->retrieve_users_from_group(
            Request :: get(Manager :: PARAM_EXTERNAL_REPOSITORY_GROUP));
        $members_list = array();
        foreach ($members as $member)
        {
            $members_list[$member->get_username()] = $member->get_username();
        }
        return $members_list;
    }
}
