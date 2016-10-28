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

class GroupUserAdditionForm extends FormValidator
{

    /**
     * The renderer used to display the form
     */
    private $bitbucket;

    public function __construct($action, $bitbucket)
    {
        parent :: __construct(ClassnameUtilities :: getInstance()->getClassnameFromObject($this, true), 'post', $action);

        $this->bitbucket = $bitbucket;
        $this->build();
    }

    public function build()
    {
        $this->addElement('text', 'user', Translation :: get('UserToAdd'));
        $this->addElement(
            'style_submit_button',
            'submit',
            Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES),
            null,
            null,
            'arrow-right');

        $this->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository\Implementation\Bitbucket', true) .
                     'PrivilegeGrantingForm.js'));
    }

    public function add_user_to_group()
    {
        $values = $this->exportValues();
        return $this->bitbucket->get_external_repository_manager_connector()->add_user_to_group(
            Request :: get(Manager :: PARAM_EXTERNAL_REPOSITORY_GROUP),
            $values['user']);
    }
}
