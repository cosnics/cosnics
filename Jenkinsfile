pipeline {
    agent any

    stages {
        stage ('Checkout') {
            steps {
               checkout scm                
            }
        }

        stage('Build') {
            steps {
                echo 'Building..'
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
