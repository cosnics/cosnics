<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn\DublinCore;

/**
 *
 * @package core\repository\implementation\matterhorn
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SeriesDublinCore extends DublinCore
{

    /**
     *
     * @return \DOMDocument
     */
    public function as_dom_document()
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        
        $tree = $document->createElementNS('http://www.opencastproject.org/xsd/1.0/dublincore/', 'dublincore');
        $document->appendChild($tree);
        
        $tree->setAttributeNS(
            'http://www.w3.org/2000/xmlns/', 
            'xmlns:xsi', 
            'http://www.w3.org/2001/XMLSchema-instance/');
        $tree->setAttributeNS(
            'http://www.w3.org/2001/XMLSchema-instance/', 
            'xsi:schemaLocation', 
            'http://www.opencastproject.org http://www.opencastproject.org/schema.xsd');
        $tree->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $tree->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:dcterms', 'http://purl.org/dc/terms/');
        $tree->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:oc', 'http://www.opencastproject.org/matterhorn');
        
        $tree->appendChild(
            $document->createElementNS('http://purl.org/dc/terms/', 'dcterms:identifier', $this->get_identifier()));
        $tree->appendChild($document->createElementNS('http://purl.org/dc/terms/', 'dcterms:title', $this->get_title()));
        $tree->appendChild(
            $document->createElementNS('http://purl.org/dc/terms/', 'dcterms:creator', $this->get_creator()));
        $tree->appendChild(
            $document->createElementNS('http://purl.org/dc/terms/', 'dcterms:contributor', $this->get_contributor()));
        $tree->appendChild(
            $document->createElementNS('http://purl.org/dc/terms/', 'dcterms:description', $this->get_description()));
        $tree->appendChild(
            $document->createElementNS('http://purl.org/dc/terms/', 'dcterms:subject', $this->get_subject()));
        $tree->appendChild(
            $document->createElementNS('http://purl.org/dc/terms/', 'dcterms:license', $this->get_license()));
        
        $tree->appendChild(
            $document->createElementNS('http://www.opencastproject.org/matterhorn', 'oc:promoted', 'true'));
        
        return $document;
    }

    /**
     *
     * @todo This shouldn't be here, but it's related to the series, so until a better solution can be found ...
     * @return string
     */
    public function get_acl()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<ns2:acl xmlns:ns2="org.opencastproject.security">
<ace><role>admin</role><action>delete</action><allow>true</allow></ace>
<ace><role>ROLE_ANONYMOUS</role><action>read</action><allow>true</allow></ace>
</ns2:acl>';
    }
}
