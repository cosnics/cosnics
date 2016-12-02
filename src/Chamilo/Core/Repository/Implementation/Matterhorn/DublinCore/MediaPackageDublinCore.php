<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn\DublinCore;

/**
 *
 * @package core\repository\implementation\matterhorn
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MediaPackageDublinCore extends DublinCore
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
        
        $tree->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:dcterms', 'http://purl.org/dc/terms/');
        $tree->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        
        $tree->appendChild($document->createElementNS('http://purl.org/dc/terms/', 'dcterms:title', $this->get_title()));
        $date = $document->createElementNS('http://purl.org/dc/terms/', 'dcterms:created', date('c'));
        $date->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:type', 'dcterms:W3CDTF');
        $tree->appendChild($date);
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
        
        return $document;
    }
}
