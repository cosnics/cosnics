<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Form;

use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider;
use Chamilo\Core\Repository\Publication\Publisher\Form\BasePublicationForm;
use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
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
        parent::__construct('publish', self::FORM_METHOD_POST, $action);

        $this->formUser = $formUser;

        $this->setSelectedContentObjects($selectedContentObjects);

        $this->buildForm();
        $this->addFooter();
        $this->setDefaults();
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
     * Builds the form by adding the necessary form elements.
     */
    public function buildForm()
    {
        $this->addSelectedContentObjects($this->formUser);

        $types = new AdvancedElementFinderElementTypes();
        $types->add_element_type($this->getUserEntityProvider()->getEntityElementFinderType());
        $types->add_element_type($this->getGroupEntityProvider()->getEntityElementFinderType());

        $this->addElement(
            'advanced_element_finder', self::PARAM_SHARE, Translation::get('ShareWith'), $types
        );
    }

    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getFormUser()
    {
        return $this->formUser;
    }

    /**
     * @return \Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider
     */
    protected function getGroupEntityProvider()
    {
        return $this->getService(GroupEntityProvider::class);
    }

    /**
     * @return \Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider
     */
    protected function getUserEntityProvider()
    {
        return $this->getService(UserEntityProvider::class);
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
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param \Chamilo\Core\User\Storage\DataClass\User[] $targetUsers
     * @param \Chamilo\Core\Group\Storage\DataClass\Group[] $targetGroups
     *
     * @throws \Exception
     */
    public function setPublicationDefaults(Publication $publication, $targetUsers, $targetGroups)
    {
        $defaults[self::PARAM_SHARE_ELEMENTS] = array();

        $groupGlyph = new FontAwesomeGlyph('users', array(), null, 'fas');
        $userGlyph = new FontAwesomeGlyph('user', array(), null, 'fas');

        $userEntityProvider = $this->getUserEntityProvider();
        $groupEntityProvider = $this->getGroupEntityProvider();

        $defaultTargets = new AdvancedElementFinderElements();

        foreach ($targetGroups as $targetGroup)
        {
            $defaultTargets->add_element($groupEntityProvider->getEntityElementFinderElement($targetGroup->getId()));
        }

        foreach ($targetUsers as $targetUser)
        {
            $defaultTargets->add_element($userEntityProvider->getEntityElementFinderElement($targetUser->getId()));
        }

        $element = $this->getElement(self::PARAM_SHARE);
        $element->setDefaultValues($defaultTargets);
        //
        //        foreach ($targetGroups as $targetGroup)
        //        {
        //            $selectedGroup = array();
        //            $selectedGroup['id'] = 'group_' . $targetGroup->getId();
        //            $selectedGroup['classes'] = $groupGlyph->getClassNamesString();
        //            $selectedGroup['title'] = $targetGroup->get_name();
        //            $selectedGroup['description'] = $targetGroup->get_name();
        //
        //            $defaults[self::PARAM_SHARE_ELEMENTS][$selectedGroup['id']] = $selectedGroup;
        //        }
        //
        //        foreach ($targetUsers as $targetUser)
        //        {
        //            $selectedUser = array();
        //            $selectedUser['id'] = 'user_' . $targetUser->getId();
        //            $selectedUser['classes'] = $userGlyph->getClassNamesString();
        //            $selectedUser['title'] = $targetUser->get_fullname();
        //            $selectedUser['description'] = $targetUser->get_username();
        //
        //            $defaults[self::PARAM_SHARE_ELEMENTS][$selectedUser['id']] = $selectedUser;
        //        }
        //
        //        if (count($defaults[self::PARAM_SHARE_ELEMENTS]) > 0)
        //        {
        //            $defaults[self::PARAM_SHARE_OPTION] = '1';
        //        }
        //
        //        $active = $this->getElement(self::PARAM_SHARE_ELEMENTS);
        //        $active->_elements[0]->setValue(serialize($defaults[self::PARAM_SHARE_ELEMENTS]));

        parent::setDefaults($defaults);
    }
}
