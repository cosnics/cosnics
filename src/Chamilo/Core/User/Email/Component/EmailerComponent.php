<?php
namespace Chamilo\Core\User\Email\Component;

use Chamilo\Core\User\Email\Form\EmailForm;
use Chamilo\Core\User\Email\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: emailer.class.php 191 2009-11-13 11:50:28Z chellee $
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
        if (PlatformSetting :: get('active_online_email_editor') == 0)
        {
            throw new NotAllowedException();
        }

        $form = new EmailForm($this->get_url(), $this->get_user(), $this->get_target_users());

        if ($form->validate())
        {
            $success = $form->email();
            $this->redirect(
                Translation :: get($success ? 'EmailSent' : 'EmailNotSent'),
                ($success ? false : true),
                array());
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

        $html[] = '<div class="content_object padding_10">';
        $html[] = '<div class="title">' . Translation :: get('SelectedUsers') . '</div>';
        $html[] = '<div class="description">';
        $html[] = '<ul class="attachments_list">';

        foreach ($target_users as $target_user)
        {
            if (is_object($target_user) && $target_user instanceof User)
            {
                $target_user = $target_user->get_fullname() . ' &lt;' . $target_user->get_email() . '&gt;';
            }

            $html[] = '<li><img src="' . Theme :: getInstance()->getCommonImagePath('Treemenu/Group') . '" alt="user"/> ' .
                 $target_user . '</li>';
        }

        $html[] = '</ul>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
