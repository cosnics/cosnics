<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Form\UserImportForm;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: importer.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * 
 * @package user.lib.user_manager.component
 */
class ImporterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageUsers');
        
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }
        
        $form = new UserImportForm(UserImportForm::TYPE_IMPORT, $this->get_url(), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->import_users();
            $message = Translation::get(
                ($success ? 'CsvUsersProcessed' : 'CsvUsersNotProcessed'), 
                array('COUNT' => $form->count_failed_items()));
            $this->redirect(
                $message . '<br />' . $form->get_failed_csv(), 
                ($success ? false : true), 
                array(Application::PARAM_ACTION => self::ACTION_IMPORT_USERS));
        }
        else
        {
            $html = array();
            
            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->display_extra_information();
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }

    public function display_extra_information()
    {
        $html = array();
        
        $html[] = '<p>' . Translation::get('CSVMustLookLike') . ' (' . Translation::get('MandatoryFields') . ')</p>';
        $html[] = '<blockquote>';
        $html[] = '<pre>';
        $text = '<b>action</b>;<b>lastname</b>;<b>firstname</b>;<b>username</b>;';
        
        $requireEmail = Configuration::getInstance()->get_setting(array(Manager::context(), 'require_email'));
        
        if ($requireEmail)
        {
            $text .= '<b>email</b>;';
        }
        else
        {
            $text .= 'email;';
        }
        
        $text .= 'language;status;active;<b>official_code</b>;phone;activation_date;expiration_date;auth_source;password';
        $html[] = $text;
        
        $text = '<b>A / U / UA / D</b>;<b>xxx</b>;<b>xxx</b>;<b>xxx</b>;';
        
        if ($requireEmail)
        {
            $text .= '<b>xxx</b>;';
        }
        else
        {
            $text .= 'xxx;';
        }
        
        $text .= 'xxx;1/5;1/0;<b>xxx</b>;xxx;date/0;date/0;platform/ldap;xxx';
        $html[] = $text;
        $html[] = '</pre>';
        $html[] = '</blockquote>';
        
        $html[] = '<p>' . Translation::get('XMLMustLookLike') . ' (' . Translation::get('MandatoryFields') . ')</p>';
        $html[] = '<blockquote>';
        $html[] = '<pre>';
        $html[] = '&lt;?xml version=&quot;1.0&quot; encoding=&quot;ISO-8859-1&quot;?&gt;';
        $html[] = '';
        $html[] = '&lt;Contacts&gt;';
        $html[] = '    &lt;Contact&gt;';
        $html[] = '        <b>&lt;action&gt;A / U / UA / D&lt;/action&gt;</b>';
        $html[] = '        <b>&lt;lastname&gt;xxx&lt;/lastname&gt;</b>';
        $html[] = '        <b>&lt;firstname&gt;xxx&lt;/firstname&gt;</b>';
        $html[] = '        <b>&lt;username&gt;xxx&lt;/username&gt;</b>';
        $html[] = '';
        $html[] = '        &lt;password&gt;xxx&lt;/password&gt;';
        
        if ($requireEmail)
        {
            $html[] = '        <b>&lt;email&gt;xxx&lt;/email&gt;</b>';
        }
        else
        {
            $html[] = '        &lt;email&gt;xxx&lt;/email&gt;';
        }
        
        $html[] = '        &lt;language&gt;xxx&lt;/language&gt;';
        $html[] = '';
        $html[] = '        &lt;status&gt;1/5&lt;/status&gt;';
        $html[] = '        &lt;active&gt;1/0&lt;/active&gt;';
        $html[] = '';
        $html[] = '        <b>&lt;official_code&gt;xxx&lt;/official_code&gt;</b>';
        $html[] = '        &lt;phone&gt;xxx&lt;/phone&gt;';
        $html[] = '';
        $html[] = '        &lt;activation_date&gt;YYYY-MM-DD HH:MM:SS/0&lt;/activation_date&gt;';
        $html[] = '        &lt;expiration_date&gt;YYYY-MM-DD HH:MM:SS/0&lt;/expiration_date&gt;';
        $html[] = '';
        $html[] = '        &lt;auth_source&gt;Platform/Ldap/Cas&lt;/auth_source&gt;';
        $html[] = '';
        $html[] = '    &lt;/Contact&gt;';
        $html[] = '&lt;/Contacts&gt;';
        $html[] = '</pre>';
        $html[] = '</blockquote>';
        
        $html[] = '<p>' . Translation::get('Details') . '</p>';
        $html[] = '<blockquote>';
        $html[] = '<u><b>' . Translation::get('Action') . '</u></b>';
        $html[] = '<br />A: ' . Translation::get('Add', null, Utilities::COMMON_LIBRARIES);
        $html[] = '<br />U: ' . Translation::get('Update', null, Utilities::COMMON_LIBRARIES);
        $html[] = '<br />UA: ' . Translation::get('UpdateOrAddUser', null, 'Chamilo\Core\User');
        $html[] = '<br />D: ' . Translation::get('Delete', null, Utilities::COMMON_LIBRARIES);
        $html[] = '<br /><br />';
        $html[] = '<u><b>' . Translation::get('Status') . '</u></b>';
        $html[] = '<br />1: ' . Translation::get('Teacher');
        $html[] = '<br />5: ' . Translation::get('Student');
        $html[] = '<br /><br />';
        $html[] = '<u><b>' . Translation::get('Date', null, Utilities::COMMON_LIBRARIES) . '</u></b>';
        $html[] = '<br />0 ' . Translation::get('NotTakenIntoAccount');
        $html[] = '<br />YYYY-MM-DD HH:MM:SS';
        $html[] = '</blockquote>';
        
        return implode($html, "\n");
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('user_importer');
    }
}
