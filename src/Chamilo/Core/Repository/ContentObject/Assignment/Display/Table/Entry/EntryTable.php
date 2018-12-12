<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry;

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
class EntryTable extends RecordTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Entry::PROPERTY_ID;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableParameters
     */
    protected $entryTableParameters;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $component
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableParameters $entryTableParameters
     *
     * @throws \Exception
     */
    public function __construct($component, EntryTableParameters $entryTableParameters)
    {
        $this->entryTableParameters = $entryTableParameters;

        parent::__construct($component);
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

        return $actions;
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableParameters
     */
    public function getEntryTableParameters(): EntryTableParameters
    {
        return $this->entryTableParameters;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableParameters $entryTableParameters
     */
    public function setEntryTableParameters(EntryTableParameters $entryTableParameters)
    {
        $this->entryTableParameters = $entryTableParameters;
    }
}