<?php
namespace Chamilo\Application\Survey\Mail\Component;

use Chamilo\Application\Survey\Mail\Manager;
use Chamilo\Application\Survey\Mail\Storage\DataClass\Mail;
use Chamilo\Application\Survey\Mail\Storage\DataClass\UserMail;
use Chamilo\Application\Survey\Mail\Storage\DataManager;
use Chamilo\Application\Survey\Mail\Table\MailRecipientTable\MailRecipientTable;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class ViewerComponent extends Manager implements TableSupport
{
    const TAB_MAIL_OVERVIEW = 1;
    const TAB_RECIPIENTS = 2;
    const TAB_QUEUED = 3;
    const TAB_UNREACHED_RECIPIENTS = 4;
    const PARAM_TABLE_TYPE = 'table_type';

    private $table_type;

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $publication_id;

    private $mail_id;

    private $selected_tab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->table_type = Request::get(self::PARAM_TABLE_TYPE, self::TAB_MAIL_OVERVIEW);
        $this->publication_id = Request::get(Manager::PARAM_PUBLICATION_ID);
        $this->mail_id = Request::get(Manager::PARAM_PUBLICATION_MAIL_ID);
        $this->selected_tab = Request::get(DynamicTabsRenderer::PARAM_SELECTED_TAB);
        
        // if (! Rights :: getInstance()->is_right_granted(Rights :: MAIL_RIGHT, $this->publication_id))
        // {
        // throw new NotAllowedException();
        // }
        
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $this->get_tabs_html();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    function get_tabs_html()
    {
        $html = array();
        
        $tabs = new DynamicVisualTabsRenderer(self::class_name());
        
        $params = $this->get_parameters();
        $params[ActionBarSearchForm::PARAM_SIMPLE_SEARCH_QUERY] = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        
        $params[self::PARAM_TABLE_TYPE] = self::TAB_MAIL_OVERVIEW;
        $tabs->add_tab(
            new DynamicVisualTab(
                self::TAB_MAIL_OVERVIEW, 
                Translation::get('Overview'), 
                Theme::getInstance()->getCommonImagePath('Action/Mail'), 
                $this->get_url($params), 
                $this->get_table_type() == self::TAB_MAIL_OVERVIEW));
        
        $params[self::PARAM_TABLE_TYPE] = self::TAB_RECIPIENTS;
        $tabs->add_tab(
            new DynamicVisualTab(
                self::TAB_RECIPIENTS, 
                Translation::get('Recipients'), 
                Theme::getInstance()->getCommonImagePath('Action/Users'), 
                $this->get_url($params), 
                $this->get_table_type() == self::TAB_RECIPIENTS));
        
        $params[self::PARAM_TABLE_TYPE] = self::TAB_QUEUED;
        $tabs->add_tab(
            new DynamicVisualTab(
                self::TAB_QUEUED, 
                Translation::get('QueuedRecipients'), 
                Theme::getInstance()->getCommonImagePath('Action/Users'), 
                $this->get_url($params), 
                $this->get_table_type() == self::TAB_QUEUED));
        
        $params[self::PARAM_TABLE_TYPE] = self::TAB_UNREACHED_RECIPIENTS;
        $tabs->add_tab(
            new DynamicVisualTab(
                self::TAB_UNREACHED_RECIPIENTS, 
                Translation::get('UnreachedRecipients'), 
                Theme::getInstance()->getCommonImagePath('Action/Users'), 
                $this->get_url($params), 
                $this->get_table_type() == self::TAB_UNREACHED_RECIPIENTS));
        
        if ($this->table_type == self::TAB_MAIL_OVERVIEW)
        {
            $tabs->set_content($this->get_mail_overview());
        }
        else
        {
            $table = new MailRecipientTable($this);
            $tabs->set_content($table->as_html());
        }
        
        $html[] = $tabs->render();
        
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_mail_overview()
    {
        $html = array();
        
        $mail = DataManager::retrieve_by_id(Mail::class_name(), $this->mail_id);
        
        $html[] = '<h3>';
        $html[] = $mail->get_mail_header();
        $html[] = '</h3>';
        
        $html[] = '<p>';
        $html[] = '<b>';
        $html[] = Translation::get('From') . ':&nbsp;';
        $html[] = '</b>';
        $html[] = $mail->get_from_address_name();
        $html[] = '&nbsp;&lt;<i>';
        $html[] = $mail->get_from_address();
        $html[] = '</i>&gt;';
        $html[] = '</p>';
        
        $html[] = '<p>';
        $html[] = '<b>';
        $html[] = Translation::get('Reply') . ':&nbsp;';
        $html[] = '</b>';
        $html[] = $mail->get_reply_address_name();
        $html[] = '&nbsp;&lt;<i>';
        $html[] = $mail->get_reply_address();
        $html[] = '</i>&gt;';
        $html[] = '</p>';
        
        $html[] = '<p>';
        $html[] = '<b>';
        $html[] = Translation::get('To') . ':&nbsp;';
        $html[] = '</b>';
        
        switch ($mail->get_type())
        {
            case 1 :
                $html[] = Translation::get("Participants");
                break;
            case 2 :
                $html[] = Translation::get("Exporters");
                break;
            case 3 :
                $html[] = Translation::get("Reporters");
                break;
        }
        
        $html[] = '</p>';
        
        $html[] = '<p>';
        $html[] = '<b>';
        $html[] = Translation::get('Sender') . ':&nbsp;';
        $html[] = '</b>';
        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_user($mail->get_sender_user_id());
        $html[] = $user->get_fullname();
        $html[] = '</p>';
        
        $html[] = '<p>';
        $html[] = '<b>';
        $html[] = Translation::get('SendDate') . ':&nbsp;';
        $html[] = '</b>';
        $html[] = date("Y-m-d H:i", $mail->get_send_date());
        $html[] = '</p>';
        
        $html[] = '<p>';
        $html[] = '<b>';
        $html[] = Translation::get('Publication') . ':&nbsp;';
        $html[] = '</b>';
        $survey_publication = DataManager::retrieve_by_id(Publication::class_name(), $mail->get_publication_id());
        $title = $survey_publication->get_title();
        $html[] = $title;
        $html[] = '</p>';
        
        $html[] = '<hr/>';
        
        $html[] = '<p>';
        $html[] = '<b>';
        $html[] = Translation::get('Message') . ':&nbsp;';
        $html[] = '</b>';
        $html[] = $mail->get_mail_content();
        $html[] = '</p>';
        
        $html[] = '<hr/>';
        
        $html[] = '<p>';
        $html[] = '<b>';
        // $html[] = Translation :: get('SentMails') . ':&nbsp;';
        // $html[] = '</b>';
        // $html[] = '<a href="' . htmlentities($this->get_view_mail_url($mail, self :: TAB_RECIPIENTS)) .
        // '#survey_mail_manager_viewer_component_2" title="' . $title . '">' . DataManager ::
        // count_survey_publication_sent_mails($mail->get_id()) . '</a>';
        // $html[] = '&nbsp;-&nbsp;';
        // $html[] = '<b>';
        // $html[] = Translation :: get('UnsentMails') . ':&nbsp;';
        // $html[] = '</b>';
        // $html[] = '<a href="' . htmlentities($this->get_view_mail_url($mail, self :: TAB_UNREACHED_RECIPIENTS)) .
        // '#survey_mail_manager_viewer_component_3" title="' . $title . '">' . DataManager ::
        // count_survey_publication_unsent_mails($mail->get_id()) . '</a>';
        // $html[] = '</p>';
        
        return implode($html, "\n");
    }

    function get_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(UserMail::class_name(), UserMail::PROPERTY_MAIL_ID), 
            new StaticConditionVariable($this->mail_id));
        
        switch ($this->get_table_type())
        {
            case self::TAB_RECIPIENTS :
                $type = UserMail::STATUS_MAIL_SEND;
                break;
            case self::TAB_QUEUED :
                $type = UserMail::STATUS_MAIL_IN_QUEUE;
                break;
            case self::TAB_UNREACHED_RECIPIENTS :
                $type = UserMail::STATUS_MAIL_NOT_SEND;
                break;
        }
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(UserMail::class_name(), UserMail::PROPERTY_STATUS), 
            new StaticConditionVariable($type));
        $condition = new AndCondition($conditions);
        return $condition;
    }

    function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar(
                $this->get_url(array(self::PARAM_TABLE_TYPE => $this->get_table_type())));
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }
        return $this->buttonToolbarRenderer;
    }

    public function get_table_condition($object_table_class_name)
    {
        return $this->get_condition();
    }

    public function get_table_type()
    {
        return $this->table_type;
    }
}
?>