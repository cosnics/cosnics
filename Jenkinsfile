pipeline {
    agent any

    stages {
        stage ('Checkout source') {
            steps {
               checkout scm                
            }
        }

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
}
