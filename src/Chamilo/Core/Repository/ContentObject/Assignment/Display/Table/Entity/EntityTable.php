<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityTable extends RecordTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Entry::PROPERTY_ENTITY_ID;

    /**
     * @var EntityTableParameters
     */
    protected $entityTableParameters;

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $component
     * @param EntityTableParameters $entityTableParameters
     *
     * @throws \Exception
     */
    public function __construct($component, EntityTableParameters $entityTableParameters)
    {
        $this->entityTableParameters = $entityTableParameters;
        parent::__construct($component);
    }

    /**
     * @return EntityTableParameters
     */
    public function getEntityTableParameters(): EntityTableParameters
    {
        return $this->entityTableParameters;
    }

    /**
     * Returns the implemented form actions
     *
     * @return TableFormActions
     */
    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DOWNLOAD,
                        Manager::PARAM_ENTITY_TYPE => $this->getEntityTableParameters()->getAssignmentServiceBridge()
                            ->getCurrentEntityType()
                    )
                ),
                Translation::get('DownloadSelected'),
                false
            )
        );

        return $actions;
    }
}