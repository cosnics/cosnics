<?php
namespace Chamilo\Core\MetadataOld\Value\Attribute\Component;

use Chamilo\Core\MetadataOld\Value\Attribute\Form\AttributeValueForm;
use Chamilo\Core\MetadataOld\Value\Attribute\Manager;
use Chamilo\Core\MetadataOld\Value\Attribute\Storage\DataClass\DefaultAttributeValue;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Controller to create the schema
 */
class CreatorComponent extends Manager
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

        $form = new AttributeValueForm(
            $this->get_url(),
            null,
            \Chamilo\Core\MetadataOld\Storage\DataManager :: retrieve_controlled_vocabulary_from_attribute(
                Request :: get(\Chamilo\Core\MetadataOld\Attribute\Manager :: PARAM_ATTRIBUTE_ID))->as_array());

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();

                $default_attribute_value = new DefaultAttributeValue();
                $default_attribute_value->set_value($values[DefaultAttributeValue :: PROPERTY_VALUE]);
                $default_attribute_value->set_attribute_vocabulary_id(
                    $values[DefaultAttributeValue :: PROPERTY_ATTRIBUTE_VOCABULARY_ID]);

                $attribute_id = Request :: get(\Chamilo\Core\MetadataOld\Attribute\Manager :: PARAM_ATTRIBUTE_ID);
                $default_attribute_value->set_attribute_id($attribute_id);

                $success = $default_attribute_value->create();

                $translation = $success ? 'ObjectCreated' : 'ObjectNotCreated';

                $message = Translation :: get(
                    $translation,
                    array('OBJECT' => Translation :: get('DefaultAttributeValue')),
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
                $this->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_BROWSE)),
                Translation :: get('BrowserComponent')));
    }
}