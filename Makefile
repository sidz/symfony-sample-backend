# See https://tech.davis-hansson.com/p/make/
MAKEFLAGS += --warn-undefined-variables
MAKEFLAGS += --no-builtin-rules

.DEFAULT_GOAL := help
.PHONY: help
help:
	@echo "\033[33mUsage:\033[0m\n  make TARGET\n\n\033[33mAvailable commands:\033[0m"
	@grep -E '^[a-zA-Z-]+:.*?## .*$$' Makefile | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  [32m%-27s[0m %s\n", $$1, $$2}'

#
# Variables
#---------------------------------------------------------------------------

CONSOLE := $(shell which bin/console)
sf_console:
ifndef CONSOLE
	@printf "Run \033[32mcomposer require cli\033[39m to install the Symfony console.\n"
endif

PHP_CS_FIXER=./vendor/bin/php-cs-fixer

#
# Commands (phony targets)
#---------------------------------------------------------------------------

cache-clear: ## Clears the cache
ifdef CONSOLE
	@bin/console cache:clear --no-warmup
else
	@rm -rf var/cache/*
endif

cs-check: $(PHP_CS_FIXER) ## Runs PHP-CS-Fixer in dry run mode
	$(PHP_CS_FIXER) fix --dry-run --config=.php_cs.dist --diff --ansi --diff-format=udiff

cs-fix: $(PHP_CS_FIXER) ## Runs PHP-CS-Fixer
	$(PHP_CS_FIXER) fix --config=.php_cs.dist --diff --ansi --diff-format=udiff
	LC_ALL=C sort -u .gitignore -o .gitignore
