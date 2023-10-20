<?php
namespace Chamilo\Core\Repository\ContentObject\GradeBook\Form;

use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditorOptions;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 * @package repository.lib.content_object.gradebook
 */
/**
 * This class represents a form to create or update gradebooks
 */
class GradeBookForm extends ContentObjectForm
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected function build_creation_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_creation_form($this->getHtmlEditorOptions(), $in_tab);
    }

    protected function build_editing_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_editing_form($this->getHtmlEditorOptions(), $in_tab);
    }

    protected function getHtmlEditorOptions()
    {
        $htmleditor_options = array();

        $htmleditor_options[FormValidatorHtmlEditorOptions::OPTION_HEIGHT] = '500';

        return $htmleditor_options;
    }
}
