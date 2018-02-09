
    pipeline {
            agent any
            options { disableConcurrentBuilds() }
            stages {
                stage('Create Build') {
                    steps {
                        echo 'Notifying slack build has started'
                        notifySlack()
                        echo 'Delete unsupported code'
                        sh 'rm -rf src/Chamilo/Application/Survey'
                        sh 'rm -rf src/Chamilo/Core/Repository/ContentObject/Survey'
                        sh 'rm -rf src/Chamilo/Core/Repository/ContentObject/Bookmark'
                        sh 'rm -rf src/Chamilo/Core/Repository/ContentObject/Matterhorn'
                        sh 'rm -rf src/Chamilo/Core/Repository/ContentObject/PhysicalLocation'
                        sh 'rm -rf src/Chamilo/Core/Repository/ContentObject/Vimeo'
                        sh 'rm -rf src/Chamilo/Core/Repository/Implementation/Bitbucket'
                        sh 'rm -rf src/Chamilo/Core/Repository/Implementation/Box'
                        sh 'rm -rf src/Chamilo/Core/Repository/Implementation/Dropbox'
                        sh 'rm -rf src/Chamilo/Core/Repository/Implementation/Flickr'
                        sh 'rm -rf src/Chamilo/Core/Repository/Implementation/Hq23'
                        sh 'rm -rf src/Chamilo/Core/Repository/Implementation/Matterhorn'
                        sh 'rm -rf src/Chamilo/Core/Repository/Implementation/Photobucket'
                        sh 'rm -rf src/Chamilo/Core/Repository/Implementation/Picase'
                        sh 'rm -rf src/Chamilo/Core/Repository/Implementation/Scribd'
                        sh 'rm -rf src/Chamilo/Core/Repository/Implementation/Slideshare'
                        sh 'rm -rf src/Chamilo/Core/Repository/Implementation/Soundcloud'
                        sh 'rm -rf src/Chamilo/Core/Repository/Implementation/Vimeo'
                        sh 'rm -rf src/Chamilo/Core/Repository/Implementation/Wikimedia'
                        sh 'rm -rf src/Chamilo/Core/Repository/Implementation/Wikipedia'
                        sh 'rm -rf src/Chamilo/Application/Weblcms/Tool/Implementation/Ephorus'
                        sh 'rm -rf src/Chamilo/Application/Weblcms/Tool/Implementation/Chat'
                        sh 'rm -rf src/Chamilo/Application/Weblcms/Tool/Implementation/Geolocation'
                        echo 'Composer update'
                        sh 'composer update -o'
                    }
                }
                stage('Deploy') {
                    steps {
                        echo 'Deploying..'
                        sh 'ansible-playbook /ansible/deploy.yml -i /ansible/hosts --extra-vars "hosts=test remote_user=jenkins project_root=/cosnics/${BRANCH_NAME} project_local_path=${WORKSPACE}/ cosnics_url=https://demo.cosnics.org/${BRANCH_NAME}/current/web/ cosnics_deploy_database_name=cosnics-${BRANCH_NAME} cosnics_deploy_test_database_name=cosnics-${BRANCH_NAME}-test"'
                    }
                }
                stage('Test') {
                    steps {
                        dir ("/cosnics/${env.BRANCH_NAME}/current/") {
                            sh "php console chamilo:phpunit:generate-config"
                            sh "bin/phpunit -c files/configuration/phpunit.xml --log-junit ${WORKSPACE}/build-reports/phpunit-result.xml"
                        }
                        step([$class: 'XUnitBuilder',
                            thresholds: [[$class: 'FailedThreshold', unstableThreshold: '0']],
                            tools: [[$class: 'JUnitType', pattern: "build-reports/*.xml"]]])
                        publishHTML([allowMissing: false, alwaysLinkToLastBuild: false, keepAll: false, reportDir: 'build-reports', reportFiles: 'index.html', reportName: 'HTML Report', reportTitles: ''])
                    }
                }
            }
        }
