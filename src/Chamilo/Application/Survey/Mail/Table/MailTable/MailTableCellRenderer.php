<?php
namespace Chamilo\Application\Survey\Mail\Table\MailTable;

use Chamilo\Application\Survey\Mail\Component\ViewerComponent;
use Chamilo\Application\Survey\Mail\Manager;
use Chamilo\Application\Survey\Mail\Storage\DataClass\Mail;
use Chamilo\Application\Survey\Mail\Storage\DataClass\UserMail;
use Chamilo\Application\Survey\Mail\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class MailTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $object)
    {
        switch ($column->get_name())
        {
            case Mail::PROPERTY_MAIL_HEADER :
                $title = parent::render_cell($column, $object);
                $title_short = $title;
                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }
                return '<a href="' . htmlentities(
                    $this->get_component()->get_view_mail_url($object, ViewerComponent::TAB_MAIL_OVERVIEW)) . '" title="' .
                     $title . '">' . $title_short . '</a>';
            
            case Translation::get('SentMails') :
                $conditions = array();
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(UserMail::class_name(), UserMail::PROPERTY_MAIL_ID), 
                    new StaticConditionVariable($object->get_id()));
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(UserMail::class_name(), UserMail::PROPERTY_STATUS), 
                    new StaticConditionVariable(UserMail::STATUS_MAIL_SEND));
                $parameters = new DataClassCountParameters(new AndCondition($conditions));
                return '<a href="' .
                     htmlentities($this->get_component()->get_view_mail_url($object, ViewerComponent::TAB_RECIPIENTS)) .
                     '" title="' . $title . '">' . DataManager::count(UserMail::class_name(), $parameters) . '</a>';
            
            case Translation::get('UnsentMails') :
                $conditions = array();
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(UserMail::class_name(), UserMail::PROPERTY_MAIL_ID), 
                    new StaticConditionVariable($object->get_id()));
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(UserMail::class_name(), UserMail::PROPERTY_STATUS), 
                    new StaticConditionVariable(UserMail::STATUS_MAIL_NOT_SEND));
                $parameters = new DataClassCountParameters(new AndCondition($conditions));
                
                return '<a href="' . htmlentities(
                    $this->get_component()->get_view_mail_url($object, ViewerComponent::TAB_UNREACHED_RECIPIENTS)) .
                     '" title="' . $title . '">' . DataManager::count(UserMail::class_name(), $parameters) . '</a>';
            case Translation::get('MailsInQueue') :
                $conditions = array();
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(UserMail::class_name(), UserMail::PROPERTY_MAIL_ID), 
                    new StaticConditionVariable($object->get_id()));
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(UserMail::class_name(), UserMail::PROPERTY_STATUS), 
                    new StaticConditionVariable(UserMail::STATUS_MAIL_IN_QUEUE));
                $parameters = new DataClassCountParameters(new AndCondition($conditions));
                
                return '<a href="' . htmlentities(
                    $this->get_component()->get_view_mail_url($object, ViewerComponent::TAB_UNREACHED_RECIPIENTS)) .
                     '" title="' . $title . '">' . DataManager::count(UserMail::class_name(), $parameters) . '</a>';
        }
        
        return parent::render_cell($column, $object);
    }

    public function get_actions($object)
    {
        $toolbar = new Toolbar();
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE, 
                        Manager::PARAM_PUBLICATION_MAIL_ID => $object->get_id())), 
                ToolbarItem::DISPLAY_ICON));
        
        return $toolbar->as_html();
    }
}
?>