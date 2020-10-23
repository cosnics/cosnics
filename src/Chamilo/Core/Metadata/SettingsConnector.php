<?php
namespace Chamilo\Core\Metadata;

use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 *
 * @package Chamilo\Core\Metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SettingsConnector
{

    /**
     *
     * @return string[]
     */
    public function retrieve_namespaces()
    {
        $namespaces = DataManager::retrieves(Schema::class, new DataClassRetrievesParameters());
        
        if ($namespaces->count())
        {
            $spaces[0] = Translation::get('SelectNamespace');
            
            foreach($namespaces as $namespace)
            {
                $spaces[$namespace->get_id()] = $namespace->get_name();
            }
        }
        else
        {
            $spaces[0] = Translation::get('NoNamespaceDefined');
        }
        
        return $spaces;
    }
}
