<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Component;

use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\Common\Import\ImportParameters;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ImporterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $form = $this->build_importing_form();

        if ($form->validate())
        {
            $object = $this->import_ical($form);

            if (count($object) > 0)
            {
                $this->redirect(
                    Translation :: get('IcalImported'),
                    false,
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_CREATE,
                        \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ID => $object,
                        \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Viewer\Manager :: ACTION_PUBLISHER));
            }
            else
            {
                $this->redirect(Translation :: get('NoEventsInFile'), true);
            }
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode("\n", $html);
        }
    }

    /**
     *
     * @return \libraries\format\FormValidator
     */
    public function build_importing_form()
    {
        $url = $this->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_IMPORT));
        $form = new FormValidator('ical_import', 'post', $url);

        $form->addElement(
            'file',
            'file',
            sprintf(
                Translation :: get('FileName', null, 'core\repository\content_object\document'),
                ini_get('upload_max_filesize', null, 'core\repository\content_object\document')));

        $allowed_upload_types = array('ics');
        $form->addRule(
            'file',
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');
        $form->addRule('file', Translation :: get('OnlyIcsAllowed'), 'filetype', $allowed_upload_types);

        $buttons = array();
        $buttons[] = $form->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Import', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'positive import'));

        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        return $form;
    }

    /**
     *
     * @param \libraries\format\FormValidator $form
     * @return boolean
     */
    public function import_ical($form)
    {
        $parameters = ImportParameters :: factory(
            ContentObjectImport :: FORMAT_ICAL,
            $this->get_user_id(),
            0,
            FileProperties :: from_upload($_FILES['file']));
        $controller = ContentObjectImportController :: factory($parameters);
        return $controller->run();
    }

    /**
     *
     * @see \libraries\architecture\application\Application::add_additional_breadcrumbs()
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('personal_calendar_ical_importer');
    }
}
