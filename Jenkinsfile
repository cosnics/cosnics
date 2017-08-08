try {
    pipeline {
            agent any

            stages {
                stage('Run Composer') {
                    steps {
                        echo 'Notifying slack build has started'
                        notifySlack()
                        echo 'Composer update'
                        sh 'composer update'
                    }
                }
                stage('Deploy') {
                    steps {
                        echo 'Deploying..'
                        sh 'ansible-playbook /ansible/deploy.yml -i /ansible/hosts --extra-vars "hosts=test remote_user=jenkins project_root=/cosnics/${BRANCH_NAME} project_local_path=${WORKSPACE}/ cosnics_deploy_database_name=cosnics-${BRANCH_NAME} cosnics_deploy_test_database_name=cosnics-${BRANCH_NAME}-test"'
                    }
                }
                stage('Test') {
                    steps {
                        dir ("/cosnics/${env.BRANCH_NAME}/current/") {
                            sh "php console chamilo:phpunit:generate-config"
                            sh "bin/phpunit -c files/configuration/phpunit.xml --log-junit ${WORKSPACE}/build-reports/phpunit-result.xml"
                        }
                        step([$class: 'XUnitBuilder',
                            thresholds: [[$class: 'FailedThreshold', unstableThreshold: '1']],
                            tools: [[$class: 'JUnitType', pattern: "build-reports/*.xml"]]])
                        publishHTML([allowMissing: false, alwaysLinkToLastBuild: false, keepAll: false, reportDir: 'build-reports', reportFiles: 'index.html', reportName: 'HTML Report', reportTitles: ''])
                    }
                }
            }
        }
     } 
     catch (e) {
        currentBuild.result = 'FAILURE'
        throw e
    } finally {
        notifySlack(currentBuild.result)
    }


def notifySlack(String buildStatus = 'STARTED') {
        // Build status of null means success.
        buildStatus = buildStatus ?: 'SUCCESS'
        
        def extraMessage = ""
        def color

        if (buildStatus == 'STARTED') {
            color = '#D4DADF'
            extraMessage = getChangeString()
        } else if (buildStatus == 'SUCCESS') {
            color = '#BDFFC3'
            extraMessage = " - <https://demo.cosnics.org/${BRANCH_NAME}|demo>"
        } else if (buildStatus == 'UNSTABLE') {
            color = '#FFFE89'
        } else {
            color = '#FF9FA1'
        }

        def msg = "BUILD ${buildStatus}: `${env.JOB_NAME}` <${env.BUILD_URL}|#${env.BUILD_NUMBER}> ${extraMessage}"

        slackSend(color: color, message: msg)
 }

@NonCPS
def getChangeString() {
    MAX_MSG_LEN = 100
    def changeString = ""
    def changeLogSets = currentBuild.changeSets
    for (int i = 0; i < changeLogSets.size(); i++) {
        def entries = changeLogSets[i].items
        for (int j = 0; j < entries.length; j++) {
            def entry = entries[j]
            truncated_msg = entry.msg.take(MAX_MSG_LEN)
            changeString += " - ${truncated_msg} [${entry.author.getFullName()}]\n"
        }
    }

    if (!changeString) {
        changeString = " - No new changes"
    }
    
    return changeString
}
