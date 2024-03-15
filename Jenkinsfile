pipeline {
    agent any

    environment {
        APP_NAME="${JOB_NAME.substring(0, JOB_NAME.lastIndexOf('/'))}"
        SHORT_COMMIT="${GIT_COMMIT[0..7]}"
        ECR_URL = "091457645177.dkr.ecr.ap-southeast-1.amazonaws.com"
        AWS_CREDENTIALS_ID = "aws-credential"
        PORTAINER_ACCESS_TOKEN = credentials('portainer-api-key')
        PORTAINER_API_URL="https://portainer.goldenowl.asia/api"
        PORTAINER_API_ENDPOINT="2"
        DOCKERFILE="Dockerfile"
    }

    stages {
        stage('Clone repository') {
            steps {
                script{
                    checkout scm
                }
            }
        }
        stage('Set branch env') {
            steps {
                script {
                   switch(GIT_BRANCH) {
                      case 'main':
                         env.ECR_REPO_APP = "remote-tracking-be"
                         env.PORTAINER_STACK_ID = "47"
                         break
                      case 'feat/cicd':
                         env.ECR_REPO_APP = "remote-tracking-be"
                         env.PORTAINER_STACK_ID = "47"
                         break
                   }
                }
            }
        }
        stage('Build app') {
            when {
                anyOf {
                    branch 'main'
                    branch 'feat/cicd'
                }
            }
            steps {
                script {
                    docker.withRegistry("https://" + "${env.ECR_URL}", "ecr:ap-southeast-1:" + env.AWS_CREDENTIALS_ID) {
                        def IMAGE_NAME="${env.ECR_URL}/${env.ECR_REPO_APP}:${env.SHORT_COMMIT}"
                        def customImage = docker.build("$IMAGE_NAME", " -f ${env.DOCKERFILE} .")
                        customImage.push()
                        customImage.push("latest")
                    }
                }
            }
        }
        stage('Update image & deploy new version') {
            when {
                anyOf {
                    branch 'main'
                    branch 'feat/cicd'
                }
            }
            steps {
                script {
                    try {
                        sh "python3 deploy/deploy.py ${env.PORTAINER_API_URL} ${env.PORTAINER_ACCESS_TOKEN} ${env.PORTAINER_API_ENDPOINT} ${env.PORTAINER_STACK_ID} ${env.SHORT_COMMIT}"
                    } catch (Exception e) {
                        echo "Caught exception: ${e}"
                        currentBuild.result = 'FAILURE'
                    }
                }
            }
        }
        stage('Cleanup') {
            steps {
                echo 'Cleaning..'
                echo 'Running docker rmi..'
            }
        }
    }
    post {
        always {
            // Clean up Docker images and temporary .env file after the build is complete
            cleanWs()
        }
    }
}
