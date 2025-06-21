pipeline {
    agent any

    environment {
        IMAGE = "your-dockerhub-username/laravel-app"
        K8S_YAML = "k8s/deployment.yaml"
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Build Image') {
            steps {
                sh 'docker build -f Dockerfile.k8s -t $IMAGE:$BUILD_NUMBER .'
                sh 'docker tag $IMAGE:$BUILD_NUMBER $IMAGE:latest'
            }
        }

        stage('Push Image') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'dockerhub-creds', usernameVariable: 'DOCKER_USER', passwordVariable: 'DOCKER_PASS')]) {
                    sh """
                        echo $DOCKER_PASS | docker login -u $DOCKER_USER --password-stdin
                        docker push $IMAGE:$BUILD_NUMBER
                        docker push $IMAGE:latest
                    """
                }
            }
        }

        stage('Deploy to K8s') {
            steps {
                withKubeConfig([credentialsId: 'kubeconfig']) {
                    sh """
                        sed 's|IMAGE_PLACEHOLDER|$IMAGE:$BUILD_NUMBER|g' $K8S_YAML | kubectl apply -f -
                    """
                }
            }
        }
    }

    post {
        success {
            echo "✅ Deployed build $BUILD_NUMBER to Kubernetes"
        }
        failure {
            echo "❌ Deployment failed"
        }
    }
}
