<?php
use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

require __DIR__ . '/../../Architecture/Bootstrap.php';
Chamilo\Libraries\Architecture\Bootstrap :: getInstance();

function convert_namespace($namespace)
{
    $namespace_parts = explode('\\', $namespace);
    $new_namespace_parts = array();

    foreach ($namespace_parts as $namespace_part)
    {
        $new_namespace_parts[] = (string) StringUtilities :: getInstance()->createString($namespace_part)->upperCamelize();
    }

    return 'Chamilo\\' . implode('\\', $new_namespace_parts);
}

header('Content-Type: text/plain');

$package_list = PlatformPackageBundles :: getInstance(PlatformPackageBundles :: MODE_ALL);
$categorized_namespaces = $package_list->get_type_packages();

$i = 0;

foreach ($categorized_namespaces as $namespace_category => $namespaces)
{
    foreach ($namespaces as $namespace)
    {
        $path = Package :: exists($namespace);

        if ($path)
        {
            $dom_document = new DOMDocument('1.0', 'UTF-8');
            $dom_document->formatOutput = true;
            $dom_document->preserveWhiteSpace = false;
            $dom_document->load($path);
            $dom_xpath = new DOMXPath($dom_document);

            // Change the context
            $context_node = $dom_xpath->query('/packages/package/context')->item(0);

            $context_value = $context_node->nodeValue;
            $context_value = trim($context_node->nodeValue);

            $new_namespace = convert_namespace($context_value);

            $context_node->nodeValue = $new_namespace;

            // Change the code
            $code_node = $dom_xpath->query('/packages/package/code')->item(0);
            $code_node->nodeValue = (string) StringUtilities :: getInstance()->createString(trim($code_node->nodeValue))->upperCamelize();

            // Change the dependency id's
            $dependency_nodes = $dom_xpath->query('/packages/package/pre-depends/dependency/id');

            foreach ($dependency_nodes as $dependency_node)
            {
                $dependency_node->nodeValue = convert_namespace(trim($dependency_node->nodeValue));
            }

            // Set the section
            $section_node = $dom_xpath->query('/packages/package/section')->item(0);
            $type_node = $dom_document->createElement(
                'type',
                ClassnameUtilities :: getInstance()->getNamespaceParent($new_namespace));

            $section_node->parentNode->replaceChild($type_node, $section_node);

            // Removing some unused elements
            $elements = array('cycle', 'filename', 'size', 'md5', 'sha1', 'sha256', 'sha512', 'tagline', 'homepage');

            foreach ($elements as $element)
            {
                $node = $dom_xpath->query('/packages/package/' . $element)->item(0);
                $node->parentNode->removeChild($node);
            }

            // $dom_document->save($path);
        }
    }
}