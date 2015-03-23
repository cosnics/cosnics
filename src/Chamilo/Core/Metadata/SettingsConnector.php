<?php
namespace Chamilo\Core\Metadata;

use Chamilo\Core\Metadata\Schema\Storage\DataClass\Schema;
use Chamilo\Core\Metadata\Storage\DataManager;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: settings_metadata_connector.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * 
 * @package metadata.settings
 */

/**
 * Simple connector class to facilitate rendering settings forms by preprocessing data from the datamanagers to a simple
 * array format.
 * 
 * @author Hans De Bisschop
 */
class SettingsConnector
{

    public function retrieve_namespaces()
    {
        $namespaces = DataManager :: retrieves(Schema :: class_name());
        
        if ($namespaces->size())
        {
            $spaces[0] = Translation :: get('SelectNamespace');
            
            while ($namespace = $namespaces->next_result())
            {
                $spaces[$namespace->get_id()] = $namespace->get_name();
            }
        }
        else
        {
            $spaces[0] = Translation :: get('NoNamespaceDefined');
        }
        
        return $spaces;
    }
}
