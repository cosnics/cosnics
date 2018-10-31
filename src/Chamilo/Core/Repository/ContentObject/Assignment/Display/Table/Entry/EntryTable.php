<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
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
abstract class EntryTable extends RecordTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Entry::PROPERTY_ID;

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
     */
    private $assignmentDataProvider;

    /**
     *
     * @var integer
     */
    private $entityId;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $component
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param integer $entityId
     */
    public function __construct($component, AssignmentDataProvider $assignmentDataProvider, $entityId)
    {
        parent::__construct($component);

        $this->assignmentDataProvider = $assignmentDataProvider;
        $this->entityId = $entityId;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
     */
    public function getAssignmentDataProvider()
    {
        return $this->assignmentDataProvider;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     */
    public function setAssignmentDataProvider(AssignmentDataProvider $assignmentDataProvider)
    {
        $this->assignmentDataProvider = $assignmentDataProvider;
    }

    /**
     *
     * @return integer
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     *
     * @param integer $entityId
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * Returns the implemented form actions
     *
     * @return TableFormActions
     */
    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, Manager::PARAM_ENTRY_ID);

        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DOWNLOAD
                    )
                ),
                Translation::get('DownloadSelected'),
                false
            )
        );

//        if($this->get_component()->getDataProvider()->canEditAssignment())
//        {
//            $actions->add_form_action(
//                new TableFormAction(
//                    $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DELETE)),
//                    Translation::get('DeleteSelected')
//                )
//            );
//        }

        return $actions;
    }
}