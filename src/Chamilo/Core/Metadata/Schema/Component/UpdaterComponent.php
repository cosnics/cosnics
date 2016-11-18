<?php
namespace Chamilo\Core\Metadata\Schema\Component;

use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Core\Metadata\Schema\Form\SchemaForm;
use Chamilo\Core\Metadata\Schema\Manager;
use Chamilo\Core\Metadata\Schema\Storage\DataManager;
use Chamilo\Core\Metadata\Service\EntityTranslationFormService;
use Chamilo\Core\Metadata\Service\EntityTranslationService;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Controller to update the schema
 * 
 * @package Chamilo\Core\Metadata\Schema\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
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
        
        $schema_id = Request::get(self::PARAM_SCHEMA_ID);
        $this->set_parameter(self::PARAM_SCHEMA_ID, $schema_id);
        
        $schema = DataManager::retrieve_by_id(Schema::class_name(), $schema_id);
        
        if ($schema->is_fixed())
        {
            throw new NotAllowedException();
        }
        
        $form = new SchemaForm($schema, new EntityTranslationFormService($schema), $this->get_url());
        
        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();
                
                $schema->set_namespace($values[Schema::PROPERTY_NAMESPACE]);
                $schema->set_name($values[Schema::PROPERTY_NAME]);
                $schema->set_url($values[Schema::PROPERTY_URL]);
                
                $success = $schema->update();
                
                if ($success)
                {
                    $entity = DataClassEntityFactory::getInstance()->getEntityFromDataClass($schema);
                    $entityTranslationService = new EntityTranslationService($entity);
                    $success = $entityTranslationService->updateEntityTranslations(
                        $values[EntityTranslationService::PROPERTY_TRANSLATION]);
                }
                
                $translation = $success ? 'ObjectUpdated' : 'ObjectNotUpdated';
                
                $message = Translation::get(
                    $translation, 
                    array('OBJECT' => Translation::get('Schema')), 
                    Utilities::COMMON_LIBRARIES);
            }
            catch (\Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }
            
            $this->redirect($message, ! $success, array(self::PARAM_ACTION => self::ACTION_BROWSE));
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
     * @param \Chamilo\Libraries\Format\Structure\BreadcrumbTrail $breadcrumb_trail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumb_trail)
    {
        $breadcrumb_trail->add(
            new Breadcrumb(
                $this->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_BROWSE), array(self::PARAM_SCHEMA_ID)), 
                Translation::get('BrowserComponent')));
    }
}