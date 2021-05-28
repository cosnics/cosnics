<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Component;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Form\AdminForm;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Manager;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataClass\Admin;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;

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
        
        $form = new AdminForm($this, $this->get_url());
        
        if ($form->validate())
        {
            $success = $this->process_admin($form->exportValues());
            
            $this->redirect(Translation::get($success ? 'AdminsSaved' : 'AdminsNotSaved'), !$success);
        }
        else
        {
            $html = [];
            
            $html[] = $this->render_header();
            $html[] = $this->get_tabs(self::ACTION_CREATE, $form->toHtml())->render();
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }

    public function process_admin($values)
    {
        foreach ($values[AdminForm::PROPERTY_ENTITIES] as $entity_type => $entity_ids)
        {
            foreach ($entity_ids as $entity_id)
            {
                foreach ($values[AdminForm::PROPERTY_TARGETS] as $target_type => $target_ids)
                {
                    foreach ($target_ids as $target_id)
                    {
                        $admin = new Admin();
                        $admin->set_origin(Admin::ORIGIN_INTERNAL);
                        $admin->set_entity_type($entity_type);
                        $admin->set_entity_id($entity_id);
                        $admin->set_target_type($target_type);
                        $admin->set_target_id($target_id);
                        
                        if (! $admin->create())
                        {
                            return false;
                        }
                    }
                }
            }
        }
        
        return true;
    }
}