<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\Component;

use Chamilo\Core\Repository\Implementation\Bitbucket\Manager;

class BatcherComponent extends Manager
{

    private static $repository_map = array(
        'chamilo/core' => 'libraries',
        'chamilo/core-admin' => 'core/admin',
        'chamilo/core-group' => 'group',
        'chamilo/core-help' => 'core/help',
        'chamilo/core-home' => 'core/home',
        'chamilo/core-install' => 'core/install',
        'chamilo/core-menu' => 'core/menu',
        'chamilo/core-migration' => 'core/migration',
        'chamilo/core-reporting' => 'reporting',
        'chamilo/core-repository' => 'core/repository',
        'chamilo/core-rights' => 'core/rights',
        'chamilo/core-tracking' => 'core/tracking',
        'chamilo/core-user' => 'user',
        'chamilo/core-context-linker' => 'core/context_linker',
        'chamilo/core-metadata' => 'core/metadata',
        'chamilo/core-lynx' => 'core/lynx',
        'chamilo/app-cache' => 'application/cache',
        'chamilo/app-cas-user' => 'application/cas_user',
        'chamilo/app-forum' => 'application/forum',
        'chamilo/app-handbook' => 'application/handbook',
        'chamilo/app-laika' => 'application/laika',
        'chamilo/app-personal-calendar' => 'application/personal_calendar',
        'chamilo/app-personal-messenger-2' => 'application/personal_messenger',
        'chamilo/app-portfolio' => 'application/portfolio',
        'chamilo/app-reservations' => 'application/reservations',
        'chamilo/app-weblcms' => 'application/weblcms',
        'chamilo/app-weblcms-announcement' => 'application/weblcms/tool/announcement',
        'chamilo/app-weblcms-appointment' => 'application/weblcms/tool/appointment',
        'chamilo/app-weblcms-assessment' => 'application/weblcms/tool/assessment',
        'chamilo/app-weblcms-assignment' => 'application/weblcms/tool/assignment',
        'chamilo/app-weblcms-blog' => 'application/weblcms/tool/blog',
        'chamilo/app-weblcms-calendar' => 'application/weblcms/tool/calendar',
        'chamilo/app-weblcms-chat' => 'application/weblcms/tool/chat',
        'chamilo/app-weblcms-course-copier' => 'application/weblcms/tool/course_copier',
        'chamilo/app-weblcms-course-deleter' => 'application/weblcms/tool/course_deleter',
        'chamilo/app-weblcms-course-truncater' => 'application/weblcms/tool/course_truncater',
        'chamilo/app-weblcms-course-group' => 'application/weblcms/tool/course_group',
        'chamilo/app-weblcms-course-sections' => 'application/weblcms/tool/course_sections',
        'chamilo/app-weblcms-course-settings' => 'application/weblcms/tool/course_settings',
        'chamilo/app-weblcms-description' => 'application/weblcms/tool/description',
        'chamilo/app-weblcms-document' => 'application/weblcms/tool/document',
        'chamilo/app-weblcms-ephorus' => 'application/weblcms/tool/ephorus',
        'chamilo/app-weblcms-forum' => 'application/weblcms/tool/forum',
        'chamilo/app-weblcms-geolocation' => 'application/weblcms/tool/geolocation',
        'chamilo/app-weblcms-glossary' => 'application/weblcms/tool/glossary',
        'chamilo/app-weblcms-home' => 'application/weblcms/tool/home',
        'chamilo/app-weblcms-learning-path' => 'application/weblcms/tool/learning_path',
        'chamilo/app-weblcms-link' => 'application/weblcms/tool/link',
        'chamilo/app-weblcms-note' => 'application/weblcms/tool/note',
        'chamilo/app-weblcms-peer-assessment' => 'application/weblcms/tool/peer_assessment',
        'chamilo/app-weblcms-reporting' => 'application/weblcms/tool/reporting',
        'chamilo/app-weblcms-rights' => 'application/weblcms/tool/rights',
        'chamilo/app-weblcms-search' => 'application/weblcms/tool/search',
        'chamilo/app-weblcms-streaming-video' => 'application/weblcms/tool/streaming_video',
        'chamilo/app-weblcms-user' => 'application/weblcms/tool/user',
        'chamilo/app-weblcms-wiki' => 'application/weblcms/tool/wiki',
        'chamilo/co-announcement' => 'repository/content_object/announcement',
        'chamilo/co-assessment' => 'repository/content_object/assessment',
        'chamilo/co-assessment-match-numeric-question' => 'repository/content_object/assessment_match_numeric_question',
        'chamilo/co-assessment-match-text-question' => 'repository/content_object/assessment_match_text_question',
        'chamilo/co-assessment-matching-question' => 'repository/content_object/assessment_matching_question',
        'chamilo/co-assessment-matrix-question' => 'repository/content_object/assessment_matrix_question',
        'chamilo/co-assessment-multiple-choice-question' => 'repository/content_object/assessment_multiple_choice_question',
        'chamilo/co-assessment-open-question' => 'repository/content_object/assessment_open_question',
        'chamilo/co-assessment-rating-question' => 'repository/content_object/assessment_rating_question',
        'chamilo/co-assessment-select-question' => 'repository/content_object/assessment_select_question',
        'chamilo/co-assignment' => 'repository/content_object/assignment',
        'chamilo/co-blog' => 'repository/content_object/blog',
        'chamilo/co-blog-item' => 'repository/content_object/blog_item',
        'chamilo/co-bookmark' => 'repository/content_object/bookmark',
        'chamilo/co-calendar-event' => 'repository/content_object/calendar_event',
        'chamilo/co-dailymotion' => 'repository/content_object/dailymotion',
        'chamilo/co-description' => 'repository/content_object/description',
        'chamilo/co-document' => 'repository/content_object/document',
        'chamilo/co-event' => 'repository/content_object/event',
        'chamilo/co-external-calendar' => 'repository/content_object/external_calendar',
        'chamilo/co-feedback' => 'repository/content_object/feedback',
        'chamilo/co-fill-in-the-blanks-question' => 'repository/content_object/fill_in_blanks_question',
        'chamilo/co-forum' => 'repository/content_object/forum',
        'chamilo/co-forum-topic' => 'repository/content_object/forum_topic',
        'chamilo/co-glossary' => 'repository/content_object/glossary',
        'chamilo/co-glossary-item' => 'repository/content_object/glossary_item',
        'chamilo/co-handbook' => 'repository/content_object/handbook',
        'chamilo/co-handbook-item' => 'repository/content_object/handbook_item',
        'chamilo/co-handbook-topic' => 'repository/content_object/handbook_topic',
        'chamilo/co-hotpotatoes' => 'repository/content_object/hotpotatoes',
        'chamilo/co-hotspot-question' => 'repository/content_object/hotspot_question',
        'chamilo/co-indicator' => 'repository/content_object/indicator',
        'chamilo/co-introduction' => 'repository/content_object/introduction',
        'chamilo/co-learning-path' => 'repository/content_object/learning_path',
        'chamilo/co-learning-path-item' => 'repository/content_object/learning_path_item',
        'chamilo/co-link' => 'repository/content_object/link',
        'chamilo/co-match-question' => 'repository/content_object/match_question',
        'chamilo/co-mediamosa' => 'repository/content_object/mediamosa',
        'chamilo/co-note' => 'repository/content_object/note',
        'chamilo/co-ordering-question' => 'repository/content_object/ordering_question',
        'chamilo/co-peer-assessment' => 'repository/content_object/peer_assessment',
        'chamilo/co-personal-message' => 'repository/content_object/personal_message',
        'chamilo/co-physical-location' => 'repository/content_object/physical_location',
        'chamilo/co-portfolio' => 'repository/content_object/portfolio',
        'chamilo/co-portfolio-item' => 'repository/content_object/portfolio_item',
        'chamilo/co-rss-feed' => 'repository/content_object/rss_feed',
        'chamilo/co-scorm-item' => 'repository/content_object/scorm_item',
        'chamilo/co-slideshare' => 'repository/content_object/slideshare',
        'chamilo/co-soundcloud' => 'repository/content_object/soundcloud',
        'chamilo/co-system-announcement' => 'repository/content_object/system_announcement',
        'chamilo/co-task' => 'repository/content_object/task',
        'chamilo/co-template' => 'repository/content_object/template',
        'chamilo/co-twitter-search' => 'repository/content_object/twitter_search',
        'chamilo/co-vimeo' => 'repository/content_object/vimeo',
        'chamilo/co-wiki' => 'repository/content_object/wiki',
        'chamilo/co-wiki-page' => 'repository/content_object/wiki_page',
        'chamilo/co-youtube' => 'repository/content_object/youtube',
        'chamilo/co-file' => 'repository/content_object/file',
        'chamilo/co-page' => 'repository/content_object/page',
        'chamilo/co-webpage' => 'repository/content_object/webpage',
        'chamilo/ext-repo-bitbucket' => 'common/extensions/external_repository_manager/implementation/bitbucket',
        'chamilo/ext-repo-box' => 'common/extensions/external_repository_manager/implementation/box',
        'chamilo/ext-repo-dailymotion' => 'common/extensions/external_repository_manager/implementation/dailymotion',
        'chamilo/ext-repo-dropbox' => 'common/extensions/external_repository_manager/implementation/dropbox',
        'chamilo/ext-repo-flickr' => 'common/extensions/external_repository_manager/implementation/flickr',
        'chamilo/ext-repo-google-docs' => 'common/extensions/external_repository_manager/implementation/google_docs',
        'chamilo/ext-repo-mediamosa' => 'common/extensions/external_repository_manager/implementation/mediamosa',
        'chamilo/ext-repo-photobucket' => 'common/extensions/external_repository_manager/implementation/photobucket',
        'chamilo/ext-repo-picasa' => 'common/extensions/external_repository_manager/implementation/picasa',
        'chamilo/ext-repo-scribd' => 'common/extensions/external_repository_manager/implementation/scribd',
        'chamilo/ext-repo-slideshare' => 'common/extensions/external_repository_manager/implementation/slideshare',
        'chamilo/ext-repo-soundcloud' => 'common/extensions/external_repository_manager/implementation/soundcloud',
        'chamilo/ext-repo-vimeo' => 'common/extensions/external_repository_manager/implementation/vimeo',
        'chamilo/ext-repo-wikimedia' => 'common/extensions/external_repository_manager/implementation/wikimedia',
        'chamilo/ext-repo-wikipedia' => 'common/extensions/external_repository_manager/implementation/wikipedia',
        'chamilo/ext-repo-youtube' => 'common/extensions/external_repository_manager/implementation/youtube');

    public function run()
    {
        $html = array();

        $html[] = $this->render_header();
        $html[] = 'Running batch action on all of the account repositories<br />' . "\n";

        $repositories = $this->retrieve_external_repository_objects();
        $connector = $this->get_external_repository_manager_connector();

        while ($repository = $repositories->next_result())
        {
            $namespace = self::$repository_map[$repository->get_id()];

            if ($namespace)
            {
                $service_configuration = array();
                $service_configuration['type'] = '';
                $service_configuration['Endpoint'] = '';
                $service_configuration['Project name'] = str_replace('/', '_', $namespace);
                $service_configuration['Token'] = '';

                if ($connector->create_repository_service($repository->get_id(), $service_configuration))
                {
                    $html[] = 'Created a ' . $service_configuration['type'] . ' service for ' . $namespace . '<br />' .
                         "\n";
                }
                else
                {
                    $html[] = '<b>FAILED</b> to create a ' . $service_configuration['type'] . ' service for ' .
                         $namespace . '<br />' . "\n";
                }
            }
        }

        $html[] = '<b>FINISHED</b> processing all repositories' . "\n";
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}
