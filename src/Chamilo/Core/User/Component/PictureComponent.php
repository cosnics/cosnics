<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\NoContextComponent;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Core\User\Form\PictureForm;

/**
 *
 * @package Chamilo\Core\User\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PictureComponent extends ProfileComponent implements NoContextComponent
{

    /**
     *
     * @var \Chamilo\Core\User\Form\PictureForm
     */
    private $form;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        // not allowed for anonymous user
        if ($this->get_user()->is_anonymous_user())
        {
            throw new NotAllowedException();
        }

        $this->form = new PictureForm($this->get_user(), $this->get_url());

        if ($this->form->validate())
        {
            $success = $this->form->update();
            if (! $success)
            {
                if (isset($_FILES['picture_uri']) && $_FILES['picture_uri']['error'])
                {
                    $neg_message = 'FileTooBig';
                }
                else
                {
                    $neg_message = 'UserProfileNotUpdated';
                }
            }
            else
            {
                $neg_message = 'UserProfileNotUpdated';
                $pos_message = 'UserProfileUpdated';
            }

            $this->redirect(
                Translation :: get($success ? $pos_message : $neg_message),
                ($success ? false : true),
                array(Application :: PARAM_ACTION => self :: ACTION_CHANGE_PICTURE));
        }
        else
        {
            return $this->renderPage();
        }
    }

    /**
     *
     * @return string
     */
    public function getContent()
    {
        return $this->form->toHtml();
    }
}
