def notifySlack(String buildStatus = 'STARTED') {
    // Build status of null means success.
    buildStatus = buildStatus ?: 'SUCCESS'

    def color

    if (buildStatus == 'STARTED') {
        color = '#D4DADF'
    } else if (buildStatus == 'SUCCESS') {
        color = '#BDFFC3'
    } else if (buildStatus == 'UNSTABLE') {
        color = '#FFFE89'
    } else {
        color = '#FF9FA1'
    }

    def msg = "${buildStatus}: `${env.JOB_NAME}` #${env.BUILD_NUMBER}:\n${env.BUILD_URL}"

    slackSend(color: color, message: msg)
}

pipeline {
    try {
        agent any

        stages {
            stage('Run Composer') {
                steps {
                    echo 'Composer update'
                    sh 'composer update'
                }
            }
            stage('Deploy') {
                steps {
                    echo 'Deploying..'
                }
            }
            stage('Test') {
                steps {
                    echo 'Deploying....'
                }
            }
        }
    } catch (e) {
        currentBuild.result = 'FAILURE'
        throw e
    } finally {
        notifySlack(currentBuild.result)
    }
}
