# Development Environment Setup

## Overview

This project uses DDEV with the `ddev-drupal-contrib` addon for local development and testing. The environment is automatically set up for the GitHub Copilot coding agent via `.github/workflows/copilot-setup-steps.yml`.

## Automatic Setup (GitHub Copilot Agent)

When the Copilot agent starts working, the environment is automatically configured with:

1. DDEV installation
2. Drupal 11 installation with all dependencies
3. Webprofiler module enabled
4. Testing infrastructure (PHPUnit, Selenium Chrome)

## Manual Setup (Local Development)

### Prerequisites

- Docker Desktop or Docker Engine
- DDEV (install from https://ddev.com/get-started/)

### Initial Setup

```bash
# Start DDEV
ddev start

# Install dependencies and set up Drupal
ddev poser
ddev symlink-project

# Install Drupal site
ddev drush site:install -y --account-name=admin --account-pass=admin

# Enable the module
ddev drush en webprofiler -y

# Access the site
ddev launch
```

## DDEV Configuration

### Key Configuration Files

- `.ddev/config.yaml`: Main DDEV configuration
- `.ddev/config.contrib.yaml`: Contrib module specific settings
- `.ddev/config.selenium-standalone-chrome.yaml`: Selenium configuration

### Environment Details

- **Project type**: Drupal 10/11
- **PHP version**: 8.3
- **Database**: MariaDB 10.6
- **Webserver**: nginx-fpm
- **Addons**: ddev-drupal-contrib, ddev-selenium-standalone-chrome

### Environment Variables

Key testing variables:
- `DRUPAL_CORE`: ^11
- `SIMPLETEST_DB`: ******db/db
- `SIMPLETEST_BASE_URL`: http://web
- `BROWSERTEST_OUTPUT_DIRECTORY`: /tmp
- `DRUPAL_TEST_WEBDRIVER_HOSTNAME`: selenium-chrome

## Common DDEV Commands

```bash
# Project management
ddev start              # Start the project
ddev stop               # Stop the project
ddev restart            # Restart the project
ddev describe           # Show project details

# Drupal commands
ddev drush cr           # Clear caches
ddev drush uli          # Get admin login link
ddev drush status       # Check Drupal status
ddev drush pml          # List modules

# Module management
ddev drush en webprofiler -y    # Enable module
ddev drush pmu webprofiler -y   # Disable module

# Database
ddev mysql              # Access MySQL CLI
ddev export-db          # Export database

# Logs
ddev logs               # View container logs
ddev drush wd-show      # View Drupal watchdog logs
```

## Module-Specific Configuration

### Enable Time Metrics

Add to `settings.php`:

```php
$settings['tracer_plugin'] = \Drupal\webprofiler\Plugin\Tracer\StopwatchTracer::class;
```

### Disable Custom Error Handler

Add to `settings.php`:

```php
$settings['webprofiler_error_page_disabled'] = TRUE;
```

## Troubleshooting

### DDEV won't start

```bash
ddev stop
ddev start
ddev describe
ddev logs
```

### Module not found

```bash
ddev symlink-project
ddev drush cr
```

### Tests failing

```bash
ddev drush status
ddev drush cr
ddev drush site:install -y
ddev drush en webprofiler -y
```

## Performance Note

Webprofiler is a development tool with performance overhead. It should never be enabled in production.
