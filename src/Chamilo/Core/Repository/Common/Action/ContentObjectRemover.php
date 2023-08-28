<?php
namespace Chamilo\Core\Repository\Common\Action;

use Chamilo\Configuration\Package\Action\Remover;
use Chamilo\Core\Repository\Storage\DataManager;

/**
 * Extension of the generic remover for content objects
 *
 * @author Hans De Bisschop
 */
abstract class ContentObjectRemover extends Remover
{
    public function extra(array $formValues): bool
    {
        $context = $this->getContext();
        $class = $context . '\\' . $this->getClassnameUtilities()->getPackageNameFromNamespace($context, true);

        $content_objects = DataManager::retrieve_content_objects($class);

        foreach ($content_objects as $content_object)
        {

            $content_object_versions = $content_object->get_content_object_versions();

            // Unlink the object
            foreach ($content_object_versions as $content_object_version)
            {
                $content_object_version->delete_links();
            }

            // Remove the object
            foreach ($content_object_versions as $content_object_version)
            {
                $content_object_version->delete();
            }
        }

        return true;
    }
}
