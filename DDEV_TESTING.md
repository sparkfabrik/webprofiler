# Testing Webprofiler Module with DDEV

This document provides instructions for testing the Webprofiler Drupal module using DDEV and Docker, both for local development and for the GitHub Copilot coding agent.

## Overview

This repository is configured with DDEV and the `ddev-drupal-contrib` addon to enable easy testing and development of the Webprofiler module. The setup allows:

- Running a complete Drupal installation with the module installed and enabled
- Running PHPUnit tests
- Running code quality tools (PHPCS, PHPStan)
- Browser testing with Selenium Chrome
- End-to-end testing of module functionality

## GitHub Copilot Agent Integration

The GitHub Copilot coding agent is preconfigured to automatically set up a DDEV environment when working on this repository. The setup is defined in `.github/workflows/copilot-setup-steps.yml` and includes:

1. Installing DDEV and required dependencies
2. Starting the DDEV project
3. Installing Drupal and project dependencies
4. Symlinking the module into the Drupal installation
5. Installing and configuring a Drupal site
6. Enabling the Webprofiler module

### How the Copilot Agent Uses DDEV

When the Copilot agent is asked to test changes or run end-to-end tests, it can use the following DDEV commands:

```bash
# Check DDEV project status
ddev describe

# Run PHPUnit tests
ddev phpunit

# Run specific test suites
ddev phpunit --testsuite=unit
ddev phpunit --testsuite=kernel
ddev phpunit --testsuite=functional

# Run code style checks
ddev phpcs

# Fix code style issues automatically
ddev phpcbf

# Run static analysis
ddev phpstan

# Access the site via browser
ddev launch

# Get admin login link
ddev drush uli

# Clear caches
ddev drush cr

# Enable/disable modules
ddev drush en webprofiler -y
ddev drush pmu webprofiler -y

# Check module status
ddev drush status
ddev drush pml | grep webprofiler

# Export database queries (webprofiler specific)
ddev drush webprofiler:export-database-data <token> <output_folder>

# Run Nightwatch JS tests (if configured)
ddev nightwatch
```

## Local Development Setup

If you want to set up the environment locally (not using the Copilot agent):

### Prerequisites

- Docker Desktop or Docker Engine
- DDEV (install from https://ddev.com/get-started/)

### Initial Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/sparkfabrik/webprofiler.git
   cd webprofiler
   ```

2. Start DDEV:
   ```bash
   ddev start
   ```

3. Install dependencies and set up Drupal:
   ```bash
   ddev poser
   ddev symlink-project
   ```

4. Install Drupal (if needed):
   ```bash
   ddev drush site:install -y --account-name=admin --account-pass=admin
   ```

5. Enable the module:
   ```bash
   ddev drush en webprofiler -y
   ```

6. Access the site:
   ```bash
   ddev launch
   ```

## Running Tests

### PHPUnit Tests

The module includes various types of tests. Run them with:

```bash
# Run all tests
ddev phpunit

# Run specific test file
ddev phpunit tests/src/Unit/SomeTest.php

# Run with verbose output
ddev phpunit --verbose

# Run with coverage (if configured)
ddev phpunit --coverage-html coverage/
```

### Code Quality Tools

```bash
# Check coding standards
ddev phpcs

# Fix coding standards automatically
ddev phpcbf

# Run static analysis
ddev phpstan

# Run all quality checks
ddev phpcs && ddev phpstan
```

### Browser Testing

If you need to test with a real browser:

1. The Selenium Chrome addon is already installed (see `.ddev/config.selenium-standalone-chrome.yaml`)
2. Run Nightwatch tests:
   ```bash
   ddev nightwatch
   ```

## DDEV Configuration

The project's DDEV configuration includes:

- **Project type**: Drupal 10/11
- **PHP version**: 8.3
- **Database**: MariaDB 10.6
- **Webserver**: nginx-fpm
- **Addons**:
  - `ddev-drupal-contrib`: Provides contrib module development workflow
  - `ddev-selenium-standalone-chrome`: Provides browser testing capabilities

### Key Configuration Files

- `.ddev/config.yaml`: Main DDEV configuration
- `.ddev/config.contrib.yaml`: Contrib module specific settings
- `.ddev/config.selenium-standalone-chrome.yaml`: Selenium configuration

### Environment Variables

The following environment variables are set for testing:

- `DRUPAL_CORE`: Core version to use (^11)
- `SIMPLETEST_DB`: Database connection for tests
- `SIMPLETEST_BASE_URL`: Base URL for tests
- `BROWSERTEST_OUTPUT_DIRECTORY`: Directory for browser test output
- `DRUPAL_TEST_WEBDRIVER_HOSTNAME`: Selenium hostname

## Module-Specific Configuration

After enabling the module, you may need to configure it:

1. Access the configuration page at `/admin/config/development/devel/webprofiler`
2. Enable the collectors you want to use
3. To enable time metrics, add to `settings.php`:
   ```php
   $settings['tracer_plugin'] = \Drupal\webprofiler\Plugin\Tracer\StopwatchTracer::class;
   ```

## Troubleshooting

### DDEV won't start

```bash
# Stop and restart
ddev stop
ddev start

# Check status
ddev describe

# Check logs
ddev logs
```

### Module not found

```bash
# Ensure the module is symlinked
ddev symlink-project

# Rebuild cache
ddev drush cr
```

### Tests failing

```bash
# Ensure database is set up
ddev drush status

# Clear caches
ddev drush cr

# Reinstall site if needed
ddev drush site:install -y
ddev drush en webprofiler -y
```

### Selenium/Browser tests not working

```bash
# Verify Selenium is running
ddev describe

# Check Selenium container logs
ddev logs -s selenium-chrome

# Restart DDEV
ddev restart
```

## Cleaning Up

```bash
# Stop DDEV
ddev stop

# Remove DDEV project (keeps code, removes containers)
ddev delete

# Clean up all DDEV projects
ddev poweroff
```

## Additional Resources

- [DDEV Documentation](https://ddev.readthedocs.io/)
- [ddev-drupal-contrib addon](https://github.com/ddev/ddev-drupal-contrib)
- [Drupal Testing Documentation](https://www.drupal.org/docs/testing)
- [GitHub Copilot Agent Documentation](https://docs.github.com/en/copilot/using-github-copilot/using-extensions/using-copilot-coding-agent)

## For GitHub Copilot Coding Agent

When you (the Copilot agent) need to test changes:

1. **The environment is already set up** via the `copilot-setup-steps.yml` workflow
2. **Run tests** using `ddev phpunit` or specific test commands
3. **Check code quality** using `ddev phpcs` and `ddev phpstan`
4. **Test manually** by accessing the site with `ddev launch` and `ddev drush uli`
5. **View logs** using `ddev logs`
6. **Make changes** to code and test iteratively

Remember: All `ddev` commands should be run from the repository root directory.
