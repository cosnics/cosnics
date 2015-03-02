<?php
namespace Chamilo\Core\Metadata\ControlledVocabulary\Component;

use Chamilo\Core\Metadata\ControlledVocabulary\Form\ControlledVocabularyForm;
use Chamilo\Core\Metadata\ControlledVocabulary\Manager;
use Chamilo\Core\Metadata\ControlledVocabulary\Storage\DataClass\ControlledVocabulary;
use Chamilo\Core\Metadata\ControlledVocabulary\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Controller to update the controlled vocabulary
 *
 * @package core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UpdaterComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $controlled_vocabulary_id = Request :: get(self :: PARAM_CONTROLLED_VOCABULARY_ID);
        $controlled_vocabulary = DataManager :: retrieve_by_id(
            ControlledVocabulary :: class_name(),
            $controlled_vocabulary_id);

        $form = new ControlledVocabularyForm($this->get_url(), $controlled_vocabulary);

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();

                $controlled_vocabulary->set_value($values[ControlledVocabulary :: PROPERTY_VALUE]);
                $success = $controlled_vocabulary->update();

                $translation = $success ? 'ObjectCreated' : 'ObjectNotCreated';

                $message = Translation :: get(
                    $translation,
                    array('OBJECT' => Translation :: get('ControlledVocabulary')),
                    Utilities :: COMMON_LIBRARIES);
            }
            catch (\Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }

            $this->redirect($message, ! $success, array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * Adds additional breadcrumbs
     *
     * @param \libraries\format\BreadcrumbTrail $breadcrumb_trail
     * @param BreadcrumbTrail $breadcrumb_trail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumb_trail)
    {
        $breadcrumb_trail->add(
            new Breadcrumb(
                $this->get_url(
                    array(Manager :: PARAM_ACTION => Manager :: ACTION_BROWSE),
                    $this->get_additional_parameters()),
                Translation :: get('BrowserComponent')));
    }

    /**
     * Returns the additional parameters
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_CONTROLLED_VOCABULARY_ID);
    }
}