# AI Agent Instructions for Webprofiler

This directory contains custom instructions for AI agents working on the Webprofiler Drupal module.

## Available Instructions

- [Development Environment Setup](./development-environment.md) - How to set up and use DDEV for testing
- [Testing Guidelines](./testing.md) - How to run tests and validate changes
- [Code Quality Standards](./code-quality.md) - Coding standards and quality checks

## Quick Reference

### Environment Setup

The GitHub Copilot coding agent environment is automatically configured via `.github/workflows/copilot-setup-steps.yml`. The workflow:

- Installs DDEV and dependencies
- Provisions Drupal 11 with the webprofiler module enabled
- Sets up testing infrastructure (PHPUnit, Selenium, etc.)

### Common Commands

```bash
# Run tests
ddev phpunit

# Check code style
ddev phpcs

# Fix code style
ddev phpcbf

# Run static analysis
ddev phpstan

# Access the site
ddev launch
ddev drush uli
```

For detailed instructions, see the specific files linked above.
