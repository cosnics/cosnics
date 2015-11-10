<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditor;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package Chamilo\Libraries\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class HtmlEditorInstanceComponent extends \Chamilo\Libraries\Ajax\Manager
{

    public function run()
    {

        // Getting some properties from the Ajax post
        $name = Request :: post('name');
        $label = Request :: post('label');

        $options = Request :: post('options');
        $options = str_replace('\"', '"', $options);
        $options = json_decode($options, true);

        $attributes = Request :: post('attributes');
        $attributes = str_replace('\"', '"', $attributes);
        $attributes = json_decode($attributes, true);

        $html_editor = FormValidatorHtmlEditor :: factory(
            LocalSetting :: getInstance()->get('html_editor'),
            $name,
            $label,
            false,
            $options,
            $attributes);

        echo $html_editor->render();
    }
}