build-nginx:
	docker image build -f resources/ops/docker/nginx/Dockerfile -t ecom-api-v3-nginx:latest --target nginx .

build-fpm:
	docker image build -f resources/ops/docker/fpm/Dockerfile -t ecom-api-v3-fpm:latest --target fpm .


deploy:
	kubectl --kubeconfig=resources/ops/kubernetes/k8s-1-22-11-do-0-sgp1-1658110584176-kubeconfig.yaml apply -f resources/ops/kubernetes/deployment.yml

