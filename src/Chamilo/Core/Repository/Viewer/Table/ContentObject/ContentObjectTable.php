<?php
namespace Chamilo\Core\Repository\Viewer\Table\ContentObject;

use Chamilo\Core\Repository\Viewer\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * This class represents a table with content objects which are candidates for publication.
 */
class ContentObjectTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_ID;

    /**
     * The user id of the current active user.
     */
    private $owner;

    /**
     * The possible types of learning objects which can be selected.
     */
    private $types;

    /**
     * The search query, or null if none.
     */
    private $query;

    /**
     * Constructor.
     * 
     * @param int $owner The id of the current user.
     * @param array $types The types of objects that can be published in current location.
     * @param string $query The search query, or null if none.
     * @param string $publish_url_format URL for publishing the selected learning object.
     * @param string $edit_and_publish_url_format URL for editing and publishing the selected learning object.
     * @see PublicationCandidateTableCellRenderer::PublicationCandidateTableCellRenderer()
     */
    public function __construct($component, $owner = null, $types = [], $query = null)
    {
        parent::__construct($component);
        $this->set_types($types);
        $this->set_owner($owner);
        $this->set_query($query);
    }

    public function get_implemented_form_actions(): TableFormActions
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        
        if ($this->get_component()->get_maximum_select() != Manager::SELECT_SINGLE)
        {
            $actions->add_form_action(
                new TableFormAction($this->get_component()->get_url(), Translation::get('PublishSelected'), false));
        }
        
        return $actions;
    }

    protected function set_types($types)
    {
        $this->types = $types;
    }

    protected function set_owner($owner)
    {
        $this->owner = $owner;
    }

    protected function set_query($query)
    {
        $this->query = $query;
    }

    protected function set_parent($parent)
    {
        $this->parent = $parent;
    }

    protected function get_types()
    {
        return $this->types;
    }

    protected function get_owner()
    {
        return $this->owner;
    }

    protected function get_query()
    {
        return $this->query;
    }

    protected function get_parent()
    {
        return $this->parent;
    }
}
