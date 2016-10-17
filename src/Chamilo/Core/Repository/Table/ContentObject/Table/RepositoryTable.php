<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Table;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository;
use Chamilo\Core\Repository\Workspace\Service\EntityRelationService;
use Chamilo\Core\Repository\Workspace\Service\EntityService;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\Table\ContentObject\Table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class RepositoryTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_CONTENT_OBJECT_ID;

    private $type;

    public function __construct($component)
    {
        parent::__construct($component);
        $template_id = FilterData::get_instance($this->get_component()->get_repository_browser()->getWorkspace())->get_type();

        if (! $template_id || ! is_numeric($template_id))
        {
            $this->type = ContentObject::class_name();
        }
        else
        {
            $template_registration = \Chamilo\Core\Repository\Configuration::registration_by_id($template_id);
            $this->type = $template_registration->get_content_object_type() . '\Storage\DataClass\\' . ClassnameUtilities::getInstance()->getPackageNameFromNamespace(
                $template_registration->get_content_object_type());
        }
    }

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        if ($this->get_component()->get_repository_browser()->getWorkspace() instanceof PersonalWorkspace)
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(Manager::PARAM_ACTION => Manager::ACTION_IMPACT_VIEW_RECYCLE)),
                    Translation::get('RemoveSelected', null, Utilities::COMMON_LIBRARIES),
                    false));
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(Manager::PARAM_ACTION => Manager::ACTION_UNLINK_CONTENT_OBJECTS)),
                    Translation::get('UnlinkSelected', null, Utilities::COMMON_LIBRARIES)));
        }

        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(Manager::PARAM_ACTION => Manager::ACTION_MOVE_CONTENT_OBJECTS)),
                Translation::get('MoveSelected', null, Utilities::COMMON_LIBRARIES),
                false));
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_PUBLICATION,
                        \Chamilo\Core\Repository\Publication\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Publication\Manager::ACTION_PUBLISH)),
                Translation::get('PublishSelected', null, Utilities::COMMON_LIBRARIES),
                false));
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(Manager::PARAM_ACTION => Manager::ACTION_EXPORT_CONTENT_OBJECTS)),
                Translation::get('ExportSelected', null, Utilities::COMMON_LIBRARIES),
                false));

        if ($this->get_component()->get_repository_browser()->getWorkspace() instanceof PersonalWorkspace)
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(
                            Application::PARAM_ACTION => Manager::ACTION_WORKSPACE,
                            \Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Manager::ACTION_SHARE)),
                    Translation::get('ShareSelected', null, Manager::context()),
                    false));
        }
        else
        {
            $entityRelationService = new EntityRelationService(new EntityRelationRepository());
            $entityService = new EntityService();

            $canDelete = $entityRelationService->hasRight(
                $entityService->getEntitiesForUser($this->get_component()->get_repository_browser()->getUser()),
                RightsService::RIGHT_DELETE,
                $this->get_component()->get_repository_browser()->getWorkspace());

            if ($canDelete)
            {
                $actions->add_form_action(
                    new TableFormAction(
                        $this->get_component()->get_url(
                            array(
                                Application::PARAM_ACTION => Manager::ACTION_WORKSPACE,
                                \Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Manager::ACTION_UNSHARE)),
                        Translation::get('UnshareSelected', null, Manager::context()),
                        false));
            }
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
