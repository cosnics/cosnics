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
            }
        }
        stage('Test') {
            steps {
                echo 'Deploying....'
            }
        }
    }
}
