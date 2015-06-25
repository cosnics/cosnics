<?php
namespace Chamilo\Core\Repository\Implementation\Youtube\Form;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class UploadForm extends FormValidator
{

    private $token;

    public function __construct()
    {
//         parent :: __construct('youtube_upload', 'post', $action);

//         $this->token = $token;
        $this->build_upload_form();
    }

    public function build_upload_form()
    {
        $this->addElement('hidden', 'token', $this->token);
        $this->addElement('file', 'file', sprintf(Translation :: get('FileName'), '2Gb'));

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Upload', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'positive'));
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
