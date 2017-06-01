try {
    notifySlack()
    pipeline {
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
                        sh 'ansible-playbook /ansible/deploy.yml -i hosts --extra-vars "hosts=test remote_user=jenkins project_root=/cosnics/${BRANCH_NAME} project_local_path=${WORKSPACE}/"'
                    }
                }
                stage('Test') {
                    steps {
                        echo 'Testing...'
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
