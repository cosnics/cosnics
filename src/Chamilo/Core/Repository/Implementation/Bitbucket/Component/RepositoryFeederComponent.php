<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\Component;

use Chamilo\Core\Repository\Implementation\Bitbucket\Manager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use DOMDocument;

/**
 *
 * @package Chamilo\Core\Repository\Implementation\Bitbucket\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RepositoryFeederComponent extends Manager
{
    const PARAM_QUERY = 'query';

    public function run()
    {
        $query = Request::get(self::PARAM_QUERY);
        $bitbucket_repositories = $this->retrieve_external_repository_objects();
        
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;
        
        $tree = $document->createElement('tree');
        $document->appendChild($tree);
        
        $repositories = $document->createElement('node');
        $tree->appendChild($repositories);
        
        $id = $document->createAttribute('id');
        $id->appendChild($document->createTextNode('0'));
        $repositories->appendChild($id);
        
        $classes = $document->createAttribute('classes');
        $classes->appendChild($document->createTextNode('category unlinked'));
        $repositories->appendChild($classes);
        
        $title = $document->createAttribute('title');
        $title->appendChild($document->createTextNode(Translation::get('Repositories')));
        $repositories->appendChild($title);
        
        while ($bitbucket_repository = $bitbucket_repositories->next_result())
        {
            if (stripos($bitbucket_repository->get_title(), $query) !== false || stripos(
                $bitbucket_repository->get_description(), 
                $query) !== false || ! $query)
            {
                $repository = $document->createElement('leaf');
                
                $id = $document->createAttribute('id');
                
                $id->appendChild($document->createTextNode('repository_' . $bitbucket_repository->get_slug()));
                $repository->appendChild($id);
                
                $classes = $document->createAttribute('classes');
                $classes->appendChild($document->createTextNode('type type_repository'));
                $repository->appendChild($classes);
                
                $title = $document->createAttribute('title');
                $title->appendChild($document->createTextNode($bitbucket_repository->get_title()));
                $repository->appendChild($title);
                
                $description = $document->createAttribute('description');
                $description->appendChild($document->createTextNode($bitbucket_repository->get_description()));
                $repository->appendChild($description);
                
                $repositories->appendChild($repository);
            }
        }
        
        echo $document->saveXML();
    }
}
