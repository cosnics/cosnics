<?php
namespace Chamilo\Core\Metadata\Schema\Component;

use Chamilo\Core\Metadata\Schema\Form\SchemaForm;
use Chamilo\Core\Metadata\Schema\Manager;
use Chamilo\Core\Metadata\Schema\Storage\DataManager;
use Chamilo\Core\Metadata\Service\EntityTranslationService;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * Controller to update the schema
 *
 * @package Chamilo\Core\Metadata\Schema\Component
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class UpdaterComponent extends Manager
{

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function run()
    {
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $schema_id = $this->getRequest()->query->get(self::PARAM_SCHEMA_ID);
        $this->set_parameter(self::PARAM_SCHEMA_ID, $schema_id);

        $schema = DataManager::retrieve_by_id(Schema::class, $schema_id);

        if ($schema->is_fixed())
        {
            throw new NotAllowedException();
        }

        $form = new SchemaForm($schema, $this->get_url());

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
                    $entity = $this->getDataClassEntityFactory()->getEntityFromDataClass($schema);
                    $success = $this->getEntityTranslationService()->updateEntityTranslations(
                        $entity, $values[EntityTranslationService::PROPERTY_TRANSLATION]
                    );
                }

                $translation = $success ? 'ObjectUpdated' : 'ObjectNotUpdated';

                $message = Translation::get(
                    $translation, ['OBJECT' => Translation::get('Schema')], StringUtilities::LIBRARIES
                );
            }
            catch (Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }

            $this->redirectWithMessage($message, !$success, [self::PARAM_ACTION => self::ACTION_BROWSE]);
        }
        else
        {
            $html = [];

            $html[] = $this->render_header();
            $html[] = $form->render();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * Adds additional breadcrumbs
     *
     * @param \Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail $breadcrumb_trail
     */
    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumb_trail): void
    {
        $breadcrumb_trail->add(
            new Breadcrumb(
                $this->get_url([Manager::PARAM_ACTION => Manager::ACTION_BROWSE], [self::PARAM_SCHEMA_ID]),
                Translation::get('BrowserComponent')
            )
        );
    }
}