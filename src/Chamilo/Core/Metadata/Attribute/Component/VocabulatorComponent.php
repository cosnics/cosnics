<?php
namespace Chamilo\Core\Metadata\Attribute\Component;

use Chamilo\Core\Metadata\Attribute\Manager;
use Chamilo\Core\Metadata\Attribute\Storage\DataClass\AttributeControlledVocabulary;
use Chamilo\Core\Metadata\Attribute\Storage\DataManager;
use Chamilo\Core\Metadata\ControlledVocabulary\Form\RelationForm;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Abstract class which manages the controlled vocabulary for a given relation
 *
 * @package core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class VocabulatorComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        $attribute_id = Request :: get(self :: PARAM_ATTRIBUTE_ID);
        if (! $attribute_id)
        {
            throw new NoObjectSelectedException(Translation :: get('Attribute'));
        }

        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $controlled_vocabularies = DataManager :: retrieve_controlled_vocabulary_from_attribute($attribute_id)->as_array();

        $form = new RelationForm($this->get_url(), $controlled_vocabularies);

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();

                $success = true;

                DataManager :: delete_controlled_vocabulary_for_attribute($attribute_id);

                foreach ($values[\Chamilo\Core\Metadata\ControlledVocabulary\Manager :: PARAM_CONTROLLED_VOCABULARY_ID][\Chamilo\Core\Metadata\ControlledVocabulary\Manager :: PARAM_CONTROLLED_VOCABULARY_ID] as $controlled_vocabulary_id)
                {
                    $relation = new AttributeControlledVocabulary();
                    $relation->set_attribute_id($attribute_id);
                    $relation->set_controlled_vocabulary_id($controlled_vocabulary_id);

                    if (! $relation->create())
                    {
                        $success = false;
                    }
                }

                $translation = $success ? 'ObjectUpdated' : 'ObjectNotUpdated';

                $message = Translation :: get(
                    $translation,
                    array('OBJECT' => Translation :: get('AttributeControlledVocabulary')),
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
                    array(self :: PARAM_ATTRIBUTE_ID)),
                Translation :: get('BrowserComponent')));
    }

    /**
     * Returns the additional parameters
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_ATTRIBUTE_ID);
    }
}