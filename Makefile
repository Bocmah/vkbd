.PHONY: docker-up-build docker-up docker-down
.DEFAULT_GOAL := help
PHP_SERVICE = php

help: ## Show this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

docker-up-build: ## Build and start services
	docker-compose up -d --build

docker-up: ## Start services
	docker-compose up -d

docker-down: ## Stop services
	docker-compose down

docker-shell: ## Jump to PHP service shell
	@docker-compose exec $(PHP_SERVICE) bash

infection: ## Run infection across all project files
	@docker-compose exec $(PHP_SERVICE) composer infection

psalm: ## Run psalm across all project files
	@docker-compose exec $(PHP_SERVICE) composer psalm
