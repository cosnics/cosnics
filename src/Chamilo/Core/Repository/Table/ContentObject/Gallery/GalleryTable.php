<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Gallery;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Service\TemplateRegistrationConsulter;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\DataClassGalleryTable\DataClassGalleryTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\Table\ContentObject\Gallery
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class GalleryTable extends DataClassGalleryTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_CONTENT_OBJECT_ID;

    private $type;

    public function __construct($component)
    {
        parent::__construct($component);

        $template_id =
            FilterData::getInstance($this->get_component()->get_repository_browser()->getWorkspace())->getType();

        if (!$template_id || !is_numeric($template_id))
        {
            $this->type = ContentObject::class;
        }
        else
        {
            $template_registration =
                $this->getTemplateRegistrationConsulter()->getTemplateRegistrationByIdentifier($template_id);

            $this->type = $template_registration->get_content_object_type() . '\\' .
                ClassnameUtilities::getInstance()->getPackageNameFromNamespace(
                    $template_registration->get_content_object_type(), true
                );
        }
    }

    /**
     * @return \Chamilo\Core\Repository\Service\TemplateRegistrationConsulter
     * @throws \Exception
     */
    public function getTemplateRegistrationConsulter()
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            TemplateRegistrationConsulter::class
        );
    }

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        if ($this->get_component()->get_repository_browser()->getWorkspace() instanceof PersonalWorkspace)
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(Manager::PARAM_ACTION => Manager::ACTION_IMPACT_VIEW_RECYCLE)
                    ), Translation::get('RemoveSelected', null, Utilities::COMMON_LIBRARIES), false
                )
            );
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(Manager::PARAM_ACTION => Manager::ACTION_UNLINK_CONTENT_OBJECTS)
                    ), Translation::get('UnlinkSelected', null, Utilities::COMMON_LIBRARIES)
                )
            );
        }

        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(Manager::PARAM_ACTION => Manager::ACTION_MOVE_CONTENT_OBJECTS)
                ), Translation::get('MoveSelected', null, Utilities::COMMON_LIBRARIES), false
            )
        );
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_PUBLICATION,
                        \Chamilo\Core\Repository\Publication\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Publication\Manager::ACTION_PUBLISH
                    )
                ), Translation::get('PublishSelected', null, Utilities::COMMON_LIBRARIES), false
            )
        );
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(Manager::PARAM_ACTION => Manager::ACTION_EXPORT_CONTENT_OBJECTS)
                ), Translation::get('ExportSelected', null, Utilities::COMMON_LIBRARIES), false
            )
        );

        if ($this->get_component()->get_repository_browser()->getWorkspace() instanceof PersonalWorkspace)
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(
                            Application::PARAM_ACTION => Manager::ACTION_WORKSPACE,
                            \Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Manager::ACTION_SHARE
                        )
                    ), Translation::get('ShareSelected'), false
                )
            );
        }
        else
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(
                            Application::PARAM_ACTION => Manager::ACTION_WORKSPACE,
                            \Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Manager::ACTION_UNSHARE
                        )
                    ), Translation::get('UnshareSelected'), false
                )
            );
        }

        return $actions;
    }

    public function get_type()
    {
        return $this->type;
    }

    public function set_type($type)
    {
        $this->type = $type;
    }
}
