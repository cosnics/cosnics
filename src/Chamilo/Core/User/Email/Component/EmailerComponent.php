<?php
namespace Chamilo\Core\User\Email\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Email\Form\EmailForm;
use Chamilo\Core\User\Email\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.common.email_manager.component
 */
class EmailerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'active_online_email_editor')) == 0)
        {
            throw new NotAllowedException();
        }

        $form = new EmailForm($this->get_url(), $this->get_user(), $this->get_target_users());

        if ($form->validate())
        {
            $success = $form->email();
            $this->redirect(
                Translation::get($success ? 'EmailSent' : 'EmailNotSent'), ($success ? false : true), array()
            );
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->display_targets();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function display_targets()
    {
        $target_users = $this->get_target_users();
        $html = array();

        $html[] = '<div class="panel panel-default">';

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . Translation::get('SelectedUsers') . '</h3>';
        $html[] = '</div>';

        $html[] = '<ul class="list-group">';

        $glyph = new FontAwesomeGlyph('users');

        foreach ($target_users as $target_user)
        {
            if (is_object($target_user) && $target_user instanceof User)
            {
                $target_user = $target_user->get_fullname() . ' &lt;' . $target_user->get_email() . '&gt;';
            }

            $html[] = '<li class="list-group-item">' . $glyph->render() . ' ' . $target_user . '</li>';
        }

        $html[] = '</ul>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
