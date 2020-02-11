<?php

use Chamilo\Libraries\Architecture\Bootstrap\Bootstrap;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\Page;

require_once realpath(__DIR__ . '/../../../../') . '/vendor/autoload.php';

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get(Bootstrap::class)->setup();

$default_views = array();
$default_views['Applications'] = 'application';
$default_views['Content Objects'] = 'repository_content_object';
$default_views['Core Common'] = 'common';
$default_views['Core Applications'] = 'core';
$default_views['External Repositories'] = 'common_extensions_external_repository_manager_implementation';
$default_views['Weblcms Tools'] = 'application_weblcms_tool';

$jenkins_jobs_path = 'E:/jenkins/';

if (!is_dir($jenkins_jobs_path))
{
    throw new \Exception('Jenkins jobs folder does not exist');
}

$jenkins_config_path = $jenkins_jobs_path . 'config.xml';

$package_list = \Chamilo\Configuration\Package\PlatformPackageBundles::getInstance()->get_type_packages();

if (!file_exists($jenkins_config_path))
{
    throw new \Exception('Jenkins config does not exist');
}

$dom_document = new \DOMDocument('1.0', 'UTF-8');
$dom_document->formatOutput = true;
$dom_document->preserveWhiteSpace = false;
$dom_document->load($jenkins_config_path);
$dom_xpath = new \DOMXPath($dom_document);
$listViews_nodes = $dom_xpath->query('/hudson/views/listView');

$existing_view_names = array();

foreach ($listViews_nodes as $listViews_node)
{
    $existing_view_names[] = $dom_xpath->query('name', $listViews_node)->item(0)->nodeValue;
}

$views_node = $dom_xpath->query('/hudson/views')->item(0);
$missing_view_names = array_diff(array_keys($default_views), $existing_view_names);

foreach ($missing_view_names as $missing_view_name)
{
    $view_node = $views_node->appendChild($dom_document->createElement('listView'));

    $owner_node = $view_node->appendChild($dom_document->createElement('owner'));
    $owner_node->appendChild($dom_document->createAttribute('class'))->appendChild(
        $dom_document->createTextNode('hudson')
    );
    $owner_node->appendChild($dom_document->createAttribute('reference'))->appendChild(
        $dom_document->createTextNode('../../..')
    );

    $view_node->appendChild($dom_document->createElement('name'))->appendChild(
        $dom_document->createTextNode($missing_view_name)
    );
    $view_node->appendChild($dom_document->createElement('filterExecutors'))->appendChild(
        $dom_document->createTextNode('false')
    );
    $view_node->appendChild($dom_document->createElement('filterQueue'))->appendChild(
        $dom_document->createTextNode('false')
    );

    $properties_node = $view_node->appendChild($dom_document->createElement('properties'));
    $properties_node->appendChild($dom_document->createAttribute('class'))->appendChild(
        $dom_document->createTextNode('hudson.model.View$PropertyList')
    );

    $jobnames_node = $view_node->appendChild($dom_document->createElement('jobNames'));
    $comparator_node = $jobnames_node->appendChild($dom_document->createElement('comparator'));
    $comparator_node->appendChild($dom_document->createAttribute('class'))->appendChild(
        $dom_document->createTextNode('hudson.util.CaseInsensitiveComparator')
    );

    $view_node->appendChild($dom_document->createElement('jobFilters'));

    $columns_node = $view_node->appendChild($dom_document->createElement('columns'));

    $columns_node->appendChild($dom_document->createElement('hudson.views.StatusColumn'));
    $columns_node->appendChild($dom_document->createElement('hudson.views.WeatherColumn'));
    $columns_node->appendChild($dom_document->createElement('hudson.views.JobColumn'));
    $columns_node->appendChild($dom_document->createElement('hudson.views.LastSuccessColumn'));
    $columns_node->appendChild($dom_document->createElement('hudson.views.LastFailureColumn'));
    $columns_node->appendChild($dom_document->createElement('hudson.views.LastDurationColumn'));
    $columns_node->appendChild($dom_document->createElement('hudson.views.BuildButtonColumn'));

    $view_node->appendChild($dom_document->createElement('includeRegex'))->appendChild(
        $dom_document->createTextNode($default_views[$missing_view_name] . '_.*')
    );
    $view_node->appendChild($dom_document->createElement('recurse'))->appendChild(
        $dom_document->createTextNode('false')
    );
}

$page = Page::getInstance();
$page->setViewMode(Page::VIEW_MODE_HEADERLESS);

if ($dom_document->save($jenkins_config_path))
{
    echo $page->getHeader()->toHtml();
    echo Display::message(Display::MESSAGE_TYPE_CONFIRM, 'Default views saved to config file');
    echo $page->getFooter()->toHtml();
}
else
{
    echo $page->getHeader()->toHtml();
    echo Display::message(Display::MESSAGE_TYPE_ERROR, 'Problem saving default views to config file');
    echo $page->getFooter()->toHtml();
}