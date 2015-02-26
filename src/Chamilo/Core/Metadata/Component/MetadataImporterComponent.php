<?php
namespace Chamilo\Core\Metadata\Component;

use Chamilo\Core\Metadata\Form\ImportFormBuilder;
use Chamilo\Core\Metadata\Import\MetadataStructureImporter;
use Chamilo\Core\Metadata\Import\Parser\XmlMetadataStructureImportParser;
use Chamilo\Core\Metadata\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;

/**
 * Component to create a new handbook_publication object
 */
class MetadataImporterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $form = new FormValidator('import_form', 'post', $this->get_url());

        $form_builder = new ImportFormBuilder($form);
        $form_builder->build_form();

        if ($form->validate())
        {
            try
            {
                $file = $_FILES[ImportFormBuilder :: FORM_ELEMENT_METADATA_FILE];

                $importer = new MetadataStructureImporter(new XmlMetadataStructureImportParser($file['tmp_name']));

                $importer->import();

                $message = Translation :: get('MetadataStructureImported');
                $success = true;
            }
            catch (\Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }

            $this->redirect($message, ! $success);
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
}
