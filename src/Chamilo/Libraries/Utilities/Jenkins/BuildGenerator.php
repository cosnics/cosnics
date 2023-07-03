<?php
namespace Chamilo\Libraries\Utilities\Jenkins;

use Chamilo\Configuration\Package\PackageList;
use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Libraries\Architecture\Bootstrap\Bootstrap;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Translation\Translation;
use Symfony\Component\Filesystem\Filesystem;

require_once realpath(__DIR__ . '/../../../../') . '/vendor/autoload.php';

class BuildGenerator
{

    /**
     * @var \configuration\package\storage\data_class\PackageList
     */
    private $package_list;

    /**
     * @var string
     */
    private $system_url;

    /**
     * @var string
     */
    private $web_url;

    /**
     * @param \configuration\package\storage\data_class\PackageList $package_list
     * @param string $web_url
     * @param string $system_url
     */
    public function __construct(PackageList $package_list, $web_url, $system_url)
    {
        $this->package_list = $package_list;
        $this->web_url = $web_url;
        $this->system_url = $system_url;
    }

    public function run()
    {
        $this->process($this->package_list);
    }

    public function getFilesystem(): Filesystem
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            Filesystem::class
        );
    }

    /**
     * @param string $context
     *
     * @return string
     */
    public function get_folder($context)
    {
        $systemPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SystemPathBuilder::class);

        return $systemPathBuilder->namespaceToFullPath($context) . 'build/config/';
    }

    /**
     * @param string $context
     *
     * @return string
     */
    public function get_job_name($context)
    {
        return str_replace('\\', '_', $context);
    }

    /**
     * @param string $context
     *
     * @return int
     */
    public function get_level($context)
    {
        return count(explode('\\', $context)) + 2;
    }

    /**
     * @return \configuration\package\storage\data_class\PackageList
     */
    public function get_package_list()
    {
        return $this->package_list;
    }

    /**
     * @param string $context
     *
     * @return string
     */
    public function get_path($context)
    {
        return str_repeat('../', $this->get_level($context));
    }

    /**
     * @param string $context
     */
    public function get_source_repository($context)
    {
        $systemPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SystemPathBuilder::class);

        $source_repository_path = $systemPathBuilder->namespaceToFullPath($context) . '.hg/hgrc';

        if (file_exists($source_repository_path))
        {
            $source_repository_configuration = parse_ini_file($source_repository_path);

            return $source_repository_configuration['default'];
        }
        else
        {
            return false;
        }
    }

    /**
     * @return string
     */
    public function get_system_url()
    {
        return $this->system_url;
    }

    /**
     * @return string
     */
    public function get_web_url()
    {
        return $this->web_url;
    }

    /**
     * @param \configuration\package\storage\data_class\PackageList $package_list
     */
    public function process(PackageList $package_list)
    {
        $this->write_build($package_list->getType());
        $this->write_phpunit($package_list->getType());

        $sub_jobs = [];

        if ($package_list->has_children())
        {
            foreach ($package_list->get_children() as $child_list)
            {
                $this->process($child_list);
                $sub_jobs[] = $this->get_job_name($child_list->getType());
            }
        }

        $this->write_configuration(
            $package_list->getType(), $sub_jobs, $this->get_source_repository($package_list->getType())
        );
    }

    /**
     * @param \configuration\package\storage\data_class\PackageList $package_list
     */
    public function set_package_list($package_list)
    {
        $this->package_list = $package_list;
    }

    /**
     * @param string $system_url
     */
    public function set_system_url($system_url)
    {
        $this->system_url = $system_url;
    }

    /**
     * @param string $web_url
     */
    public function set_web_url($web_url)
    {
        $this->web_url = $web_url;
    }

    /**
     * @param string $context
     */
    public function write_build($context)
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>
<project name="' . $context . '" default="build">
    <resolvepath propertyName="package-directory" file="../../"/>
    <import file="' . $this->get_path($context) . 'libraries/php/build/build.xml" />
</project>';

        $path = $this->get_folder($context) . 'build.xml';
        $this->getFilesystem()->dumpFile($path, $content);
    }

    /**
     * @param string $context
     * @param string[] $sub_jobs
     * @param string $source_respository
     */
    public function write_configuration($context, $sub_jobs, $source_respository)
    {
        $chart_url =
            $this->get_web_url() . ClassnameUtilities::getInstance()->namespaceToPath($context) . '/build/chart/';
        $workspace_url = $this->get_system_url() . ClassnameUtilities::getInstance()->namespaceToPath($context) . '/';

        $systemPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SystemPathBuilder::class);

        $php_class_path = $systemPathBuilder->namespaceToFullPath($context) . 'php/';
        $has_php_classes = is_dir($php_class_path);

        $content = '<?xml version="1.0" encoding="UTF-8"?>
<project>
    <actions/>
    <description>' . Translation::get('TypeName', null, $context) . '&#xd;
        &#xd;
        &lt;p&gt;Dependencies&#xd;
        &lt;img src=&quot;' . $chart_url . 'dependencies.svg&quot; /&gt;&#xd;
        Pyramid&#xd;
        &lt;img src=&quot;' . $chart_url . 'overview-pyramid.svg&quot; /&gt;&#xd;
        &lt;/p&gt;</description>
    <logRotator>
        <daysToKeep>-1</daysToKeep>
        <numToKeep>15</numToKeep>
        <artifactDaysToKeep>-1</artifactDaysToKeep>
        <artifactNumToKeep>-1</artifactNumToKeep>
    </logRotator>
    <keepDependencies>false</keepDependencies>
    <properties/>';

        if ($source_respository)
        {
            $content .= '
    <scm class="hudson.plugins.mercurial.MercurialSCM">
        <source>' . $source_respository . '</source>
        <branch>core</branch>
        <modules></modules>
        <clean>false</clean>
        <browser class="hudson.plugins.mercurial.browser.BitBucket">
            <url>' . $source_respository . '</url>
        </browser>
    </scm>';
        }
        else
        {
            $content .= '
    <scm class="hudson.scm.NullSCM"/>';
        }

        $content .= '
    <canRoam>true</canRoam>
    <disabled>false</disabled>
    <blockBuildWhenDownstreamBuilding>false</blockBuildWhenDownstreamBuilding>
    <blockBuildWhenUpstreamBuilding>false</blockBuildWhenUpstreamBuilding>
    <authToken>502ca597634db</authToken>
    <triggers class="vector"/>
    <concurrentBuild>false</concurrentBuild>
    <customWorkspace>' . $workspace_url . '</customWorkspace>
    <builders>
        <hudson.plugins.phing.PhingBuilder>
            <buildFile>build/config/build.xml</buildFile>
            <useModuleRoot>true</useModuleRoot>
        </hudson.plugins.phing.PhingBuilder>
    </builders>
    <publishers>';

        if (count($sub_jobs) > 0)
        {
            $content .= '
        <hudson.tasks.BuildTrigger>
            <childProjects>' . implode(', ', $sub_jobs) . '</childProjects>
            <threshold>
                <name>FAILURE</name>
                <ordinal>2</ordinal>
                <color>RED</color>
            </threshold>
        </hudson.tasks.BuildTrigger>';
        }

        if ($has_php_classes)
        {
            $content .= '
        <hudson.plugins.checkstyle.CheckStylePublisher>
            <healthy></healthy>
            <unHealthy></unHealthy>
            <thresholdLimit>low</thresholdLimit>
            <pluginName>[CHECKSTYLE]</pluginName>
            <defaultEncoding></defaultEncoding>
            <canRunOnFailed>false</canRunOnFailed>
            <useDeltaValues>false</useDeltaValues>
            <thresholds>
                <unstableTotalAll></unstableTotalAll>
                <unstableTotalHigh></unstableTotalHigh>
                <unstableTotalNormal></unstableTotalNormal>
                <unstableTotalLow></unstableTotalLow>
                <failedTotalAll></failedTotalAll>
                <failedTotalHigh></failedTotalHigh>
                <failedTotalNormal></failedTotalNormal>
                <failedTotalLow></failedTotalLow>
            </thresholds>
            <shouldDetectModules>false</shouldDetectModules>
            <dontComputeNew>true</dontComputeNew>
            <doNotResolveRelativePaths>false</doNotResolveRelativePaths>
            <pattern>build/log/phpcs.xml</pattern>
        </hudson.plugins.checkstyle.CheckStylePublisher>
        <hudson.plugins.pmd.PmdPublisher>
            <healthy></healthy>
            <unHealthy></unHealthy>
            <thresholdLimit>low</thresholdLimit>
            <pluginName>[PMD]</pluginName>
            <defaultEncoding></defaultEncoding>
            <canRunOnFailed>false</canRunOnFailed>
            <useDeltaValues>false</useDeltaValues>
            <thresholds>
                <unstableTotalAll></unstableTotalAll>
                <unstableTotalHigh></unstableTotalHigh>
                <unstableTotalNormal></unstableTotalNormal>
                <unstableTotalLow></unstableTotalLow>
                <failedTotalAll></failedTotalAll>
                <failedTotalHigh></failedTotalHigh>
                <failedTotalNormal></failedTotalNormal>
                <failedTotalLow></failedTotalLow>
            </thresholds>
            <shouldDetectModules>false</shouldDetectModules>
            <dontComputeNew>true</dontComputeNew>
            <doNotResolveRelativePaths>false</doNotResolveRelativePaths>
            <pattern>build/log/phpmd.xml</pattern>
        </hudson.plugins.pmd.PmdPublisher>
        <hudson.plugins.dry.DryPublisher>
            <healthy></healthy>
            <unHealthy></unHealthy>
            <thresholdLimit>low</thresholdLimit>
            <pluginName>[DRY]</pluginName>
            <defaultEncoding></defaultEncoding>
            <canRunOnFailed>false</canRunOnFailed>
            <useDeltaValues>false</useDeltaValues>
            <thresholds>
                <unstableTotalAll></unstableTotalAll>
                <unstableTotalHigh></unstableTotalHigh>
                <unstableTotalNormal></unstableTotalNormal>
                <unstableTotalLow></unstableTotalLow>
                <failedTotalAll></failedTotalAll>
                <failedTotalHigh></failedTotalHigh>
                <failedTotalNormal></failedTotalNormal>
                <failedTotalLow></failedTotalLow>
            </thresholds>
            <shouldDetectModules>false</shouldDetectModules>
            <dontComputeNew>true</dontComputeNew>
            <doNotResolveRelativePaths>false</doNotResolveRelativePaths>
            <pattern>build/log/pmd-cpd.xml</pattern>
            <highThreshold>50</highThreshold>
            <normalThreshold>25</normalThreshold>
        </hudson.plugins.dry.DryPublisher>
        <hudson.plugins.plot.PlotPublisher>
            <plots>
                <hudson.plugins.plot.Plot>
                    <title>A - Lines of code</title>
                    <yaxis>Lines of Code</yaxis>
                    <series>
                        <hudson.plugins.plot.CSVSeries>
                            <file>build/log/phploc.csv</file>
                            <label></label>
                            <fileType>csv</fileType>
                            <strExclusionSet>
                                <string>Comment Lines of Code (CLOC)</string>
                                <string>Lines of Code (LOC)</string>
                                <string>Non-Comment Lines of Code (NCLOC)</string>
                            </strExclusionSet>
                            <inclusionFlag>INCLUDE_BY_STRING</inclusionFlag>
                            <exclusionValues>Lines of Code (LOC),Comment Lines of Code (CLOC),Non-Comment Lines of Code
                                (NCLOC)
                            </exclusionValues>
                            <url></url>
                            <displayTableFlag>false</displayTableFlag>
                        </hudson.plugins.plot.CSVSeries>
                    </series>
                    <group>phploc</group>
                    <numBuilds>100</numBuilds>
                    <csvFileName>123.csv</csvFileName>
                    <csvLastModification>0</csvLastModification>
                    <style>line</style>
                    <useDescr>false</useDescr>
                </hudson.plugins.plot.Plot>
                <hudson.plugins.plot.Plot>
                    <title>B - Structures</title>
                    <yaxis>Count</yaxis>
                    <series>
                        <hudson.plugins.plot.CSVSeries>
                            <file>build/log/phploc.csv</file>
                            <label></label>
                            <fileType>csv</fileType>
                            <strExclusionSet>
                                <string>Functions</string>
                                <string>Classes</string>
                                <string>Namespaces</string>
                                <string>Files</string>
                                <string>Directories</string>
                                <string>Methods</string>
                                <string>Interfaces</string>
                                <string>Constants</string>
                                <string>Anonymous Functions</string>
                            </strExclusionSet>
                            <inclusionFlag>INCLUDE_BY_STRING</inclusionFlag>
                            <exclusionValues>Directories,Files,Namespaces,Interfaces,Classes,Methods,Functions,Anonymous
                                Functions,Constants
                            </exclusionValues>
                            <url></url>
                            <displayTableFlag>false</displayTableFlag>
                        </hudson.plugins.plot.CSVSeries>
                    </series>
                    <group>phploc</group>
                    <numBuilds>100</numBuilds>
                    <csvFileName>1107599928.csv</csvFileName>
                    <csvLastModification>0</csvLastModification>
                    <style>line</style>
                    <useDescr>false</useDescr>
                </hudson.plugins.plot.Plot>
                <hudson.plugins.plot.Plot>
                    <title>G - Average Length</title>
                    <yaxis>Average Non-Comment Lines of Code</yaxis>
                    <series>
                        <hudson.plugins.plot.CSVSeries>
                            <file>build/log/phploc.csv</file>
                            <label></label>
                            <fileType>csv</fileType>
                            <strExclusionSet>
                                <string>Average Method Length (NCLOC)</string>
                                <string>Average Class Length (NCLOC)</string>
                            </strExclusionSet>
                            <inclusionFlag>INCLUDE_BY_STRING</inclusionFlag>
                            <exclusionValues>Average Class Length (NCLOC),Average Method Length (NCLOC)
                            </exclusionValues>
                            <url></url>
                            <displayTableFlag>false</displayTableFlag>
                        </hudson.plugins.plot.CSVSeries>
                    </series>
                    <group>phploc</group>
                    <numBuilds>100</numBuilds>
                    <csvFileName>523405415.csv</csvFileName>
                    <csvLastModification>0</csvLastModification>
                    <style>line</style>
                    <useDescr>false</useDescr>
                </hudson.plugins.plot.Plot>
                <hudson.plugins.plot.Plot>
                    <title>H - Relative Cyclomatic Complexity</title>
                    <yaxis>Cyclomatic Complexity by Structure</yaxis>
                    <series>
                        <hudson.plugins.plot.CSVSeries>
                            <file>build/log/phploc.csv</file>
                            <label></label>
                            <fileType>csv</fileType>
                            <strExclusionSet>
                                <string>Cyclomatic Complexity / Lines of Code</string>
                                <string>Cyclomatic Complexity / Number of Methods</string>
                            </strExclusionSet>
                            <inclusionFlag>INCLUDE_BY_STRING</inclusionFlag>
                            <exclusionValues>Cyclomatic Complexity / Lines of Code,Cyclomatic Complexity / Number of
                                Methods
                            </exclusionValues>
                            <url></url>
                            <displayTableFlag>false</displayTableFlag>
                        </hudson.plugins.plot.CSVSeries>
                    </series>
                    <group>phploc</group>
                    <numBuilds>100</numBuilds>
                    <csvFileName>186376189.csv</csvFileName>
                    <csvLastModification>0</csvLastModification>
                    <style>line</style>
                    <useDescr>false</useDescr>
                </hudson.plugins.plot.Plot>
                <hudson.plugins.plot.Plot>
                    <title>D - Types of Classes</title>
                    <yaxis>Count</yaxis>
                    <series>
                        <hudson.plugins.plot.CSVSeries>
                            <file>build/log/phploc.csv</file>
                            <label></label>
                            <fileType>csv</fileType>
                            <strExclusionSet>
                                <string>Abstract Classes</string>
                                <string>Classes</string>
                                <string>Concrete Classes</string>
                            </strExclusionSet>
                            <inclusionFlag>INCLUDE_BY_STRING</inclusionFlag>
                            <exclusionValues>Classes,Abstract Classes,Concrete Classes</exclusionValues>
                            <url></url>
                            <displayTableFlag>false</displayTableFlag>
                        </hudson.plugins.plot.CSVSeries>
                    </series>
                    <group>phploc</group>
                    <numBuilds>100</numBuilds>
                    <csvFileName>594356163.csv</csvFileName>
                    <csvLastModification>0</csvLastModification>
                    <style>line</style>
                    <useDescr>false</useDescr>
                </hudson.plugins.plot.Plot>
                <hudson.plugins.plot.Plot>
                    <title>E - Types of Methods</title>
                    <yaxis>Count</yaxis>
                    <series>
                        <hudson.plugins.plot.CSVSeries>
                            <file>build/log/phploc.csv</file>
                            <label></label>
                            <fileType>csv</fileType>
                            <strExclusionSet>
                                <string>Methods</string>
                                <string>Static Methods</string>
                                <string>Non-Static Methods</string>
                                <string>Public Methods</string>
                                <string>Non-Public Methods</string>
                            </strExclusionSet>
                            <inclusionFlag>INCLUDE_BY_STRING</inclusionFlag>
                            <exclusionValues>Methods,Non-Static Methods,Static Methods,Public Methods,Non-Public
                                Methods
                            </exclusionValues>
                            <url></url>
                            <displayTableFlag>false</displayTableFlag>
                        </hudson.plugins.plot.CSVSeries>
                    </series>
                    <group>phploc</group>
                    <numBuilds>100</numBuilds>
                    <csvFileName>1019987862.csv</csvFileName>
                    <csvLastModification>0</csvLastModification>
                    <style>line</style>
                    <useDescr>false</useDescr>
                </hudson.plugins.plot.Plot>
                <hudson.plugins.plot.Plot>
                    <title>F - Types of Constants</title>
                    <yaxis>Count</yaxis>
                    <series>
                        <hudson.plugins.plot.CSVSeries>
                            <file>build/log/phploc.csv</file>
                            <label></label>
                            <fileType>csv</fileType>
                            <strExclusionSet>
                                <string>Class Constants</string>
                                <string>Global Constants</string>
                                <string>Constants</string>
                            </strExclusionSet>
                            <inclusionFlag>INCLUDE_BY_STRING</inclusionFlag>
                            <exclusionValues>Constants,Global Constants,Class Constants</exclusionValues>
                            <url></url>
                            <displayTableFlag>false</displayTableFlag>
                        </hudson.plugins.plot.CSVSeries>
                    </series>
                    <group>phploc</group>
                    <numBuilds>100</numBuilds>
                    <csvFileName>217648577.csv</csvFileName>
                    <csvLastModification>0</csvLastModification>
                    <style>line</style>
                    <useDescr>false</useDescr>
                </hudson.plugins.plot.Plot>
                <hudson.plugins.plot.Plot>
                    <title>C - Testing</title>
                    <yaxis>Count</yaxis>
                    <series>
                        <hudson.plugins.plot.CSVSeries>
                            <file>build/log/phploc.csv</file>
                            <label></label>
                            <fileType>csv</fileType>
                            <strExclusionSet>
                                <string>Functions</string>
                                <string>Classes</string>
                                <string>Test Methods</string>
                                <string>Methods</string>
                                <string>Test Clases</string>
                            </strExclusionSet>
                            <inclusionFlag>INCLUDE_BY_STRING</inclusionFlag>
                            <exclusionValues>Classes,Methods,Functions,Test Clases,Test Methods</exclusionValues>
                            <url></url>
                            <displayTableFlag>false</displayTableFlag>
                        </hudson.plugins.plot.CSVSeries>
                    </series>
                    <group>phploc</group>
                    <numBuilds>100</numBuilds>
                    <csvFileName>174807245.csv</csvFileName>
                    <csvLastModification>0</csvLastModification>
                    <style>line</style>
                    <useDescr>false</useDescr>
                </hudson.plugins.plot.Plot>
            </plots>
        </hudson.plugins.plot.PlotPublisher>
        <org.jenkinsci.plugins.cloverphp.CloverPublisher>
            <publishHtmlReport>true</publishHtmlReport>
            <reportDir>build/coverage</reportDir>
            <xmlLocation>build/log/clover.xml</xmlLocation>
            <disableArchiving>false</disableArchiving>
            <healthyTarget>
                <methodCoverage>10</methodCoverage>
                <statementCoverage>10</statementCoverage>
<elementCoverage>10</elementCoverage>
            </healthyTarget>
            <unhealthyTarget/>
            <failingTarget/>
        </org.jenkinsci.plugins.cloverphp.CloverPublisher>
        <htmlpublisher.HtmlPublisher>
            <reportTargets>
                <htmlpublisher.HtmlPublisherTarget>
                    <reportName>API Documentation</reportName>
                    <reportDir>build/api</reportDir>
                    <reportFiles>index.html</reportFiles>
                    <keepAll>false</keepAll>
                    <wrapperName>htmlpublisher-wrapper.html</wrapperName>
                </htmlpublisher.HtmlPublisherTarget>
                <htmlpublisher.HtmlPublisherTarget>
                    <reportName>Code Browser</reportName>
                    <reportDir>build/code_browser</reportDir>
                    <reportFiles>index.html</reportFiles>
                    <keepAll>false</keepAll>
                    <wrapperName>htmlpublisher-wrapper.html</wrapperName>
                </htmlpublisher.HtmlPublisherTarget>
            </reportTargets>
        </htmlpublisher.HtmlPublisher>
        <xunit>
            <types>
                <PHPUnitJunitHudsonTestType>
                    <pattern>build/log/junit.xml</pattern>
                    <faildedIfNotNew>false</faildedIfNotNew>
                    <deleteOutputFiles>true</deleteOutputFiles>
                    <stopProcessingIfError>false</stopProcessingIfError>
                </PHPUnitJunitHudsonTestType>
            </types>
            <thresholds>
                <org.jenkinsci.plugins.xunit.threshold.FailedThreshold>
                    <unstableThreshold>0</unstableThreshold>
                    <unstableNewThreshold></unstableNewThreshold>
                    <failureThreshold>0</failureThreshold>
                    <failureNewThreshold></failureNewThreshold>
                </org.jenkinsci.plugins.xunit.threshold.FailedThreshold>
                <org.jenkinsci.plugins.xunit.threshold.SkippedThreshold>
                    <unstableThreshold></unstableThreshold>
                    <unstableNewThreshold></unstableNewThreshold>
                    <failureThreshold></failureThreshold>
                    <failureNewThreshold></failureNewThreshold>
                </org.jenkinsci.plugins.xunit.threshold.SkippedThreshold>
            </thresholds>
            <thresholdMode>2</thresholdMode>
        </xunit>
        <hudson.plugins.jdepend.JDependRecorder>
            <configuredJDependFile>build/log/jdepend.xml</configuredJDependFile>
        </hudson.plugins.jdepend.JDependRecorder>
        <hudson.plugins.violations.ViolationsPublisher>
            <config>
                <suppressions class="tree-set">
                    <no-comparator/>
                </suppressions>
                <typeConfigs>
                    <no-comparator/>
                    <entry>
                        <string>checkstyle</string>
                        <hudson.plugins.violations.TypeConfig>
                            <type>checkstyle</type>
                            <min>10</min>
                            <max>999</max>
                            <unstable>999</unstable>
                            <usePattern>false</usePattern>
                            <pattern>build/log/phpcs.xml</pattern>
                        </hudson.plugins.violations.TypeConfig>
                    </entry>
                    <entry>
                        <string>codenarc</string>
                        <hudson.plugins.violations.TypeConfig>
                            <type>codenarc</type>
                            <min>10</min>
                            <max>999</max>
                            <unstable>999</unstable>
                            <usePattern>false</usePattern>
                            <pattern></pattern>
                        </hudson.plugins.violations.TypeConfig>
                    </entry>
                    <entry>
                        <string>cpd</string>
                        <hudson.plugins.violations.TypeConfig>
                            <type>cpd</type>
                            <min>10</min>
                            <max>999</max>
                            <unstable>999</unstable>
                            <usePattern>false</usePattern>
                            <pattern>build/log/pmd-cpd.xml</pattern>
                        </hudson.plugins.violations.TypeConfig>
                    </entry>
                    <entry>
                        <string>cpplint</string>
                        <hudson.plugins.violations.TypeConfig>
                            <type>cpplint</type>
                            <min>10</min>
                            <max>999</max>
                            <unstable>999</unstable>
                            <usePattern>false</usePattern>
                            <pattern></pattern>
                        </hudson.plugins.violations.TypeConfig>
                    </entry>
                    <entry>
                        <string>csslint</string>
                        <hudson.plugins.violations.TypeConfig>
                            <type>csslint</type>
                            <min>10</min>
                            <max>999</max>
                            <unstable>999</unstable>
                            <usePattern>false</usePattern>
                            <pattern></pattern>
                        </hudson.plugins.violations.TypeConfig>
                    </entry>
                    <entry>
                        <string>findbugs</string>
                        <hudson.plugins.violations.TypeConfig>
                            <type>findbugs</type>
                            <min>10</min>
                            <max>999</max>
                            <unstable>999</unstable>
                            <usePattern>false</usePattern>
                            <pattern></pattern>
                        </hudson.plugins.violations.TypeConfig>
                    </entry>
                    <entry>
                        <string>fxcop</string>
                        <hudson.plugins.violations.TypeConfig>
                            <type>fxcop</type>
                            <min>10</min>
                            <max>999</max>
                            <unstable>999</unstable>
                            <usePattern>false</usePattern>
                            <pattern></pattern>
                        </hudson.plugins.violations.TypeConfig>
                    </entry>
                    <entry>
                        <string>gendarme</string>
                        <hudson.plugins.violations.TypeConfig>
                            <type>gendarme</type>
                            <min>10</min>
                            <max>999</max>
                            <unstable>999</unstable>
                            <usePattern>false</usePattern>
                            <pattern></pattern>
                        </hudson.plugins.violations.TypeConfig>
                    </entry>
                    <entry>
                        <string>jcreport</string>
                        <hudson.plugins.violations.TypeConfig>
                            <type>jcreport</type>
                            <min>10</min>
                            <max>999</max>
                            <unstable>999</unstable>
                            <usePattern>false</usePattern>
                            <pattern></pattern>
                        </hudson.plugins.violations.TypeConfig>
                    </entry>
                    <entry>
                        <string>jslint</string>
                        <hudson.plugins.violations.TypeConfig>
                            <type>jslint</type>
                            <min>10</min>
                            <max>999</max>
                            <unstable>999</unstable>
                            <usePattern>false</usePattern>
                            <pattern></pattern>
                        </hudson.plugins.violations.TypeConfig>
                    </entry>
                    <entry>
                        <string>pep8</string>
                        <hudson.plugins.violations.TypeConfig>
                            <type>pep8</type>
                            <min>10</min>
                            <max>999</max>
                            <unstable>999</unstable>
                            <usePattern>false</usePattern>
                            <pattern></pattern>
                        </hudson.plugins.violations.TypeConfig>
                    </entry>
                    <entry>
                        <string>pmd</string>
                        <hudson.plugins.violations.TypeConfig>
                            <type>pmd</type>
                            <min>10</min>
                            <max>999</max>
                            <unstable>999</unstable>
                            <usePattern>false</usePattern>
                            <pattern>build/log/phpmd.xml</pattern>
                        </hudson.plugins.violations.TypeConfig>
                    </entry>
                    <entry>
                        <string>pylint</string>
                        <hudson.plugins.violations.TypeConfig>
                            <type>pylint</type>
                            <min>10</min>
                            <max>999</max>
                            <unstable>999</unstable>
                            <usePattern>false</usePattern>
                            <pattern></pattern>
                        </hudson.plugins.violations.TypeConfig>
                    </entry>
                    <entry>
                        <string>simian</string>
                        <hudson.plugins.violations.TypeConfig>
                            <type>simian</type>
                            <min>10</min>
                            <max>999</max>
                            <unstable>999</unstable>
                            <usePattern>false</usePattern>
                            <pattern></pattern>
                        </hudson.plugins.violations.TypeConfig>
                    </entry>
                    <entry>
                        <string>stylecop</string>
                        <hudson.plugins.violations.TypeConfig>
                            <type>stylecop</type>
                            <min>10</min>
                            <max>999</max>
                            <unstable>999</unstable>
                            <usePattern>false</usePattern>
                            <pattern></pattern>
                        </hudson.plugins.violations.TypeConfig>
                    </entry>
                </typeConfigs>
                <limit>100</limit>
                <sourcePathPattern></sourcePathPattern>
                <fauxProjectPath></fauxProjectPath>
                <encoding>default</encoding>
            </config>
        </hudson.plugins.violations.ViolationsPublisher>';
        }

        $content .= '
    </publishers>
    <buildWrappers/>
</project>';

        $path = $this->get_folder($context) . 'config.xml';
        $this->getFilesystem()->dumpFile($path, $content);
    }

    /**
     * @param string $context
     */
    public function write_phpunit($context)
    {
        $content = '<phpunit bootstrap="' . $this->get_path($context) . 'libraries/architecture/php/lib/test/bootstrap.php">
	<testsuites>
		<testsuite name="Source">
			<directory suffix="_test.class.php">../../test/php/source</directory>
		</testsuite>
        <testsuite name="Unit">
            <directory suffix="_test.class.php">../../test/php/unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory suffix="_test.class.php">../../test/php/integration</directory>
        </testsuite>
	</testsuites>
	<filter>
		<whitelist>
			<directory suffix=".php">../../php</directory>
		</whitelist>
	</filter>
</phpunit>';

        $path = $this->get_folder($context) . 'phpunit.xml';
        $this->getFilesystem()->dumpFile($path, $content);
    }
}

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get(Bootstrap::class)->setup();

$package_list = PlatformPackageBundles::getInstance()->get_package_list();

$web_url = 'http://10.2.201.104/html/jenkins/dev/';
$system_url = '/var/www/html/jenkins/dev/';

$generator = new BuildGenerator($package_list, $web_url, $system_url);
$generator->run();