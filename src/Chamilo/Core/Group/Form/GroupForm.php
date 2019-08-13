<?php

namespace Chamilo\Core\Group\Form;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Menu\GroupMenu;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Utilities\Utilities;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package groups.lib.forms
 */
class GroupForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'GroupUpdated';
    const RESULT_ERROR = 'GroupUpdateFailed';

    private $group;

    private $user;
    /**
     * @var \Chamilo\Core\Group\Service\GroupService
     */
    private $groupService;
    /**
     * @var \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     */
    private $exceptionLogger;

    /**
     * @var int
     */
    private $form_type;
    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * GroupForm constructor.
     *
     * @param int $form_type
     * @param Group $group
     * @param string $action
     * @param User $user
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface $exceptionLogger
     * @param \Symfony\Component\Translation\Translator $translator
     *
     * @throws \Exception
     */
    public function __construct(
        $form_type, $group, $action, $user, GroupService $groupService, ExceptionLoggerInterface $exceptionLogger,
        Translator $translator
    )
    {
        parent::__construct('groups_settings', 'post', $action);

        $this->group = $group;
        $this->user = $user;
        $this->form_type = $form_type;

        $this->groupService = $groupService;
        $this->exceptionLogger = $exceptionLogger;
        $this->translator = $translator;

        if ($this->form_type == self::TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self::TYPE_CREATE)
        {
            $this->build_creation_form();
        }

        $this->setDefaults();
    }

    public function build_basic_form()
    {
        $this->addElement('text', Group::PROPERTY_NAME, $this->translator->trans('Name', [], Manager::context()), array("size" => "50"));
        $this->addRule(
            Group::PROPERTY_NAME,
            $this->translator->trans('ThisFieldIsRequired', [], Utilities::COMMON_LIBRARIES),
            'required'
        );

        $this->addElement('text', Group::PROPERTY_CODE, $this->translator->trans('Code', [], Manager::context()), array("size" => "50"));
        $this->addRule(
            Group::PROPERTY_CODE,
            $this->translator->trans('ThisFieldIsRequired', [], Utilities::COMMON_LIBRARIES),
            'required'
        );

        $this->addElement('select', Group::PROPERTY_PARENT_ID, $this->translator->trans('Location', [], Manager::context()), $this->get_groups());
        $this->addRule(
            Group::PROPERTY_PARENT_ID,
            $this->translator->trans('ThisFieldIsRequired', [], Utilities::COMMON_LIBRARIES),
            'required'
        );

        // Disk Quota
        $this->addElement('text', Group::PROPERTY_DISK_QUOTA, $this->translator->trans('DiskQuota', [], Manager::context()), array("size" => "50"));
        $this->addRule(
            Group::PROPERTY_DISK_QUOTA,
            $this->translator->trans('ThisFieldMustBeNumeric', [], Utilities::COMMON_LIBRARIES),
            'numeric',
            null,
            'server'
        );
        // Database Quota
        $this->addElement(
            'text',
            Group::PROPERTY_DATABASE_QUOTA,
            $this->translator->trans('DatabaseQuota', [], Manager::context()),
            array("size" => "50")
        );
        $this->addRule(
            Group::PROPERTY_DATABASE_QUOTA,
            $this->translator->trans('ThisFieldMustBeNumeric', [], Utilities::COMMON_LIBRARIES),
            'numeric',
            null,
            'server'
        );

        $this->add_html_editor(
            Group::PROPERTY_DESCRIPTION,
            $this->translator->trans('Description', [], Utilities::COMMON_LIBRARIES),
            false
        );
    }

    public function build_editing_form()
    {
        $this->build_basic_form();

        $this->addElement('hidden', Group::PROPERTY_ID);

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            $this->translator->trans('Update', [], Utilities::COMMON_LIBRARIES),
            null,
            null,
            'arrow-right'
        );
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            $this->translator->trans('Reset', [], Utilities::COMMON_LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_creation_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            $this->translator->trans('Create', [], Utilities::COMMON_LIBRARIES)
        );
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            $this->translator->trans('Reset', [], Utilities::COMMON_LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function update_group()
    {
        $group = $this->group;
        $values = $this->exportValues();

        $group->set_name($values[Group::PROPERTY_NAME]);
        $group->set_description($values[Group::PROPERTY_DESCRIPTION]);
        $group->set_code($values[Group::PROPERTY_CODE]);
        $group->set_database_quota(intval($values[Group::PROPERTY_DATABASE_QUOTA]));
        $group->set_disk_quota(intval($values[Group::PROPERTY_DISK_QUOTA]));

        try
        {
            $this->groupService->updateGroup($group);
            $value = true;
        }
        catch (\Exception $ex)
        {
            $this->exceptionLogger->logException($ex);
            $value = false;
        }

        $new_parent = $values[Group::PROPERTY_PARENT_ID];
        if ($group->get_parent_id() != $new_parent)
        {
//            $group->move($new_parent);
            try
            {
                $this->groupService->moveGroup($group, $new_parent);
            }
            catch (\Exception $ex)
            {
                $this->exceptionLogger->logException($ex);
                $value = false;
            }
        }

        if ($value)
        {
            Event::trigger(
                'Update',
                Manager::context(),
                array(
                    \Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Storage\DataClass\Change::PROPERTY_REFERENCE_ID => $group->getId(
                    ),
                    \Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Storage\DataClass\Change::PROPERTY_USER_ID => $this->user->getId(
                    )
                )
            );
        }

        return $value;
    }

    public function create_group()
    {
        $group = $this->group;
        $values = $this->exportValues();

        $group->set_name($values[Group::PROPERTY_NAME]);
        $group->set_description($values[Group::PROPERTY_DESCRIPTION]);
        $group->set_code($values[Group::PROPERTY_CODE]);
        $group->set_parent_id($values[Group::PROPERTY_PARENT_ID]);
        if ($values[Group::PROPERTY_DATABASE_QUOTA] != '')
        {
            $group->set_database_quota(intval($values[Group::PROPERTY_DATABASE_QUOTA]));
        }
        if ($values[Group::PROPERTY_DISK_QUOTA] != '')
        {
            $group->set_disk_quota(intval($values[Group::PROPERTY_DISK_QUOTA]));
        }

        try
        {
            $this->groupService->createGroup($group);
            $value = true;
        }
        catch (\Exception $ex)
        {
            $this->exceptionLogger->logException($ex);
            $value = false;
        }

        if ($value)
        {
            Event::trigger(
                'Create',
                Manager::context(),
                array(
                    \Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Storage\DataClass\Change::PROPERTY_REFERENCE_ID => $group->getId(
                    ),
                    \Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Storage\DataClass\Change::PROPERTY_USER_ID => $this->user->getId(
                    )
                )
            );
        }

        return $value;
    }

    /**
     * Sets default values.
     *
     * @param array $defaults Default values for this form's parameters.
     * @param null $filter
     *
     * @throws \Exception
     */
    public function setDefaults($defaults = array(), $filter = null)
    {
        $group = $this->group;
        $defaults[Group::PROPERTY_ID] = $group->getId();
        $defaults[Group::PROPERTY_PARENT_ID] = $group->get_parent_id();
        $defaults[Group::PROPERTY_NAME] = $group->get_name();
        $defaults[Group::PROPERTY_CODE] = $group->get_code();
        $defaults[Group::PROPERTY_DESCRIPTION] = $group->get_description();
        $defaults[Group::PROPERTY_DATABASE_QUOTA] = $group->get_database_quota();
        $defaults[Group::PROPERTY_DISK_QUOTA] = $group->get_disk_quota();
        parent::setDefaults($defaults);
    }

    public function get_group()
    {
        return $this->group;
    }

    public function get_groups()
    {
        $group = $this->group;

        $group_menu = new GroupMenu($group->getId(), null, true, true, true);
        $renderer = new OptionsMenuRenderer();
        $group_menu->render($renderer, 'sitemap');

        return $renderer->toArray();
    }
}
