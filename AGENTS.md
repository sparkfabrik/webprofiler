# AI Agent Instructions for Webprofiler

This file provides instructions for AI agents working on the Webprofiler Drupal module.

## Available Instructions

### Core Documentation

- **[DDEV Commands and Usage](./ai/ddev.md)** - Complete DDEV CLI reference for automated testing, project management, and debugging
- **[Development Environment Setup](./ai/development-environment.md)** - How to set up and use DDEV for testing
- **[Testing Guidelines](./ai/testing.md)** - How to run tests and validate changes
- **[Code Quality Standards](./ai/code-quality.md)** - Coding standards and quality checks
- **[Webprofiler Configuration](./ai/webprofiler.md)** - Module-specific configuration and frontend verification

### External Resources

- **[Context7 Drupal Documentation](https://context7.com/drupal/drupal)** - Access up-to-date Drupal API documentation and code examples via the Context7 MCP server. Use the `context7-resolve-library-id` and `context7-query-docs` tools to query Drupal core and contributed module documentation.

## Quick Start

The GitHub Copilot coding agent environment is automatically configured via `.github/workflows/copilot-setup-steps.yml`. The workflow:

- Installs DDEV and dependencies
- Provisions Drupal 11 with the webprofiler module enabled
- Sets up testing infrastructure (PHPUnit, Selenium, etc.)
- Configures webprofiler settings

## Essential Workflow

When making changes, follow this workflow:

1. **Make code changes** to the module
2. **Clear caches**: `ddev drush cr`
3. **Run tests**: `ddev phpunit`
4. **Check code quality**: `ddev phpcs && ddev phpstan`
5. **Fix code style issues**: `ddev phpcbf` (if needed)
6. **Test manually** (if UI changes): `ddev drush uli`

## Pre-Completion Checklist

Before marking work complete, ensure:

- [ ] All tests pass: `ddev phpunit`
- [ ] Code standards met: `ddev phpcs` (no errors)
- [ ] Static analysis passes: `ddev phpstan` (no errors)
- [ ] Caches cleared: `ddev drush cr`
- [ ] Manual testing done (if UI changes)
- [ ] Webprofiler frontend verified (if relevant)
- [ ] No unintended files in commit

## Getting Help

For detailed command reference and troubleshooting, see the specific documentation files linked above.

**Most Important Resources:**
- [DDEV Commands](./ai/ddev.md) - For all DDEV operations and debugging
- [Webprofiler Configuration](./ai/webprofiler.md) - For module-specific setup and testing
