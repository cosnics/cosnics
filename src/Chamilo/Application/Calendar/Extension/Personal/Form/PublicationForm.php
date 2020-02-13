<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Form;

use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\Publication\Publisher\Form\BasePublicationForm;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal\Form
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationForm extends BasePublicationForm
{
    // Parameters
    const PARAM_SHARE = 'share_users_and_groups';
    const PARAM_SHARE_ELEMENTS = 'share_users_and_groups_elements';
    const PARAM_SHARE_OPTION = 'share_users_and_groups_option';

    /**
     * The publication that will be changed (when using this form to edit a publication)
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $formUser;

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $formUser
     * @param $action
     * @param array $selectedContentObjects
     *
     * @throws \Exception
     */
    public function __construct(User $formUser, $action, $selectedContentObjects = array())
    {
        parent::__construct('publish', 'post', $action);

        $this->formUser = $formUser;

        $this->setSelectedContentObjects($selectedContentObjects);

        $this->buildForm();
        $this->addFooter();
        $this->setDefaults();
    }

    /**
     * @param string[] $defaultValues
     * @param string[] $filter
     *
     * @throws \Exception
     */
    public function setDefaults($defaultValues = null, $filter = null)
    {
        $defaultValues[self::PARAM_SHARE_OPTION] = 0;

        parent::setDefaults($defaultValues, $filter);
    }

    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getFormUser()
    {
        return $this->formUser;
    }

    /**
     * Builds the form by adding the necessary form elements.
     */
    public function buildForm()
    {
        $this->addSelectedContentObjects($this->formUser);

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
        $attributes['exclude'] = array('user_' . $this->formUser->get_id());
        $attributes['defaults'] = array();

        $this->add_receivers(
            self::PARAM_SHARE, Translation::get('ShareWith', null, Utilities::COMMON_LIBRARIES), $attributes, 'Nobody'
        );
    }

    public function addFooter()
    {
        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Publish', null, Utilities::COMMON_LIBRARIES), null, null,
            new FontAwesomeGlyph('arrow-right')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param \Chamilo\Core\User\Storage\DataClass\User[] $targetUsers
     * @param \Chamilo\Core\Group\Storage\DataClass\Group[] $targetGroups
     *
     * @throws \Exception
     */
    public function setPublicationDefaults(Publication $publication, $targetUsers, $targetGroups)
    {
        $defaults[self::PARAM_SHARE_ELEMENTS] = array();

        foreach ($targetGroups as $targetGroup)
        {
            $selectedGroup = array();
            $selectedGroup['id'] = 'group_' . $targetGroup->getId();
            $selectedGroup['classes'] = 'type type_group';
            $selectedGroup['title'] = $targetGroup->get_name();
            $selectedGroup['description'] = $targetGroup->get_name();

            $defaults[self::PARAM_SHARE_ELEMENTS][$selectedGroup['id']] = $selectedGroup;
        }

        foreach ($targetUsers as $targetUser)
        {
            $selectedUser = array();
            $selectedUser['id'] = 'user_' . $targetUser->getId();
            $selectedUser['classes'] = 'type type_user';
            $selectedUser['title'] = $targetUser->get_fullname();
            $selectedUser['description'] = $targetUser->get_username();

            $defaults[self::PARAM_SHARE_ELEMENTS][$selectedUser['id']] = $selectedUser;
        }

        if (count($defaults[self::PARAM_SHARE_ELEMENTS]) > 0)
        {
            $defaults[self::PARAM_SHARE_OPTION] = '1';
        }

        $active = $this->getElement(self::PARAM_SHARE_ELEMENTS);
        $active->_elements[0]->setValue(serialize($defaults[self::PARAM_SHARE_ELEMENTS]));

        parent::setDefaults($defaults);
    }
}
