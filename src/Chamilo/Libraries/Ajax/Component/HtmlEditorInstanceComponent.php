<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Ajax\Manager;
use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditorRenderer;

/**
 * @package Chamilo\Libraries\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class HtmlEditorInstanceComponent extends Manager
{

    /**
     * @throws \QuickformException
     */
    public function run()
    {
        $request = $this->getRequest();
        $name = $request->request->get('name');
        $label = $request->request->get('label');
        $options = $request->request->get('options');
        $attributes = $request->request->get('attributes');

        $options = str_replace('\"', '"', $options);
        $options = json_decode($options, true);

        $attributes = str_replace('\"', '"', $attributes);
        $attributes = json_decode($attributes, true);

        echo $this->getFormValidatorHtmlEditorRenderer()->renderHtmlEditor($name, $label, false, $options, $attributes);
    }

    public function getFormValidatorHtmlEditorRenderer(): FormValidatorHtmlEditorRenderer
    {
        return $this->getService(FormValidatorHtmlEditorRenderer::class);
    }
}