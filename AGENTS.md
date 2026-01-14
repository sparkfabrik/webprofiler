# AI Agent Instructions for Webprofiler

This file provides instructions for AI agents working on the Webprofiler Drupal module.

## Available Instructions

- [Development Environment Setup](./ai/development-environment.md) - How to set up and use DDEV for testing
- [Testing Guidelines](./ai/testing.md) - How to run tests and validate changes
- [Code Quality Standards](./ai/code-quality.md) - Coding standards and quality checks

## Automated Testing with DDEV

After the environment is set up (via `.github/workflows/copilot-setup-steps.yml`), use DDEV commands to test changes automatically.

### Getting Help

```bash
# List all available DDEV commands
ddev

# Get help for any command
ddev help <command>
ddev <command> -h

# Examples
ddev help snapshot
ddev describe -h
```

### Testing Workflow

When making changes, always follow this workflow:

1. **Make code changes** to the module
2. **Clear caches**: `ddev drush cr`
3. **Run tests**: `ddev phpunit`
4. **Check code quality**: `ddev phpcs && ddev phpstan`
5. **Fix code style issues**: `ddev phpcbf` (if needed)
6. **Test manually** (if UI changes): `ddev drush uli`

### Essential DDEV Commands

#### Project Management
```bash
ddev start              # Start the project
ddev stop               # Stop the project
ddev restart            # Restart the project
ddev describe           # Show project details and URLs (aliases: status, st, desc)
ddev list               # Show all projects and their state
ddev poweroff           # Stop all DDEV projects
ddev launch             # Open project in web browser
```

#### Testing Commands
```bash
# Run all PHPUnit tests
ddev phpunit

# Run specific test suites
ddev phpunit --testsuite=unit
ddev phpunit --testsuite=kernel
ddev phpunit --testsuite=functional

# Run specific test file
ddev phpunit tests/src/Unit/MyTest.php

# Run specific test method
ddev phpunit --filter testMyMethod

# Run with coverage (if configured)
ddev phpunit --coverage-html coverage/
```

#### Code Quality Commands
```bash
# Check coding standards
ddev phpcs

# Fix coding standards automatically
ddev phpcbf

# Run static analysis
ddev phpstan

# Lint JavaScript
ddev eslint

# Lint CSS
ddev stylelint
```

#### Drupal Commands
```bash
# Clear all caches (run after code changes)
ddev drush cr

# Get one-time login link
ddev drush uli

# Check Drupal status
ddev drush status

# Enable module
ddev drush en webprofiler -y

# Disable module
ddev drush pmu webprofiler -y

# List modules
ddev drush pml

# Run database updates
ddev drush updb -y

# Export configuration
ddev drush cex -y

# Import configuration
ddev drush cim -y
```

#### Database Commands
```bash
# Access MySQL CLI
ddev mysql

# Export database
ddev export-db --file=/tmp/db.sql.gz

# Export without compression
ddev export-db --gzip=false --file=/tmp/db.sql

# Import database
ddev import-db --file=/tmp/db.sql.gz

# Snapshot database (for quick restore)
ddev snapshot

# Snapshot with custom name
ddev snapshot --name=before-changes

# List all snapshots
ddev snapshot --list

# Restore latest snapshot
ddev snapshot restore

# Restore specific snapshot
ddev snapshot restore --name=before-changes

# Delete all snapshots
ddev snapshot --cleanup

# Delete specific snapshot
ddev snapshot --cleanup --name=before-changes
```

#### Debugging Commands
```bash
# View logs
ddev logs

# View logs for specific service
ddev logs -s web
ddev logs -s db

# Follow logs in real-time
ddev logs -f

# View Drupal watchdog logs
ddev drush wd-show

# View recent errors
ddev drush wd-show --severity=Error

# Execute command in container
ddev exec <command>

# SSH into web container
ddev ssh

# Enable Xdebug
ddev xdebug on

# Disable Xdebug
ddev xdebug off

# Toggle Xdebug
ddev xdebug toggle

# Check Xdebug status
ddev xdebug status
```

#### Composer Commands
```bash
# Install dependencies
ddev composer install

# Update dependencies
ddev composer update

# Require new package
ddev composer require vendor/package

# Require dev package
ddev composer require --dev vendor/package
```

### Webprofiler-Specific Configuration

After enabling the webprofiler module, configure it properly:

```bash
# Enable time metrics collection
ddev exec "echo \"\\\$settings['tracer_plugin'] = \\\Drupal\\\webprofiler\\\Plugin\\\Tracer\\\StopwatchTracer::class;\" >> web/sites/default/settings.php"

# Configure webprofiler settings via drush
ddev drush config:set webprofiler.settings purge_on_cache_clear 1 -y
ddev drush config:set webprofiler.settings intercept_redirects 1 -y

# Clear cache to apply settings
ddev drush cr

# Verify configuration
ddev drush config:get webprofiler.settings

# Access webprofiler settings UI
# Visit: /admin/config/development/devel/webprofiler
```

### Verifying Webprofiler Frontend

Use Playwright to verify the webprofiler toolbar and dashboard are working:

```bash
# Get admin login link
ddev drush uli

# Use Playwright browser tools to:
# 1. Navigate to the site with the login link
# 2. Visit any page to verify toolbar appears
# 3. Click on toolbar items to verify data collection
# 4. Navigate to /admin/config/development/devel/webprofiler to verify settings page
# 5. Check that profiler data is being collected and displayed
```

For frontend testing with Playwright:
- Verify the webprofiler toolbar appears on HTML pages
- Check that toolbar items are clickable and display data
- Verify the profiler dashboard at the admin URL works correctly
- Test that time metrics, database queries, and other collectors show data

### Automated Testing Best Practices

1. **Always clear caches** before running tests: `ddev drush cr`
2. **Run tests in order**: Unit → Kernel → Functional
3. **Check code quality** before committing: `ddev phpcs && ddev phpstan`
4. **Fix auto-fixable issues**: Use `ddev phpcbf` for code style
5. **Verify manually** for UI changes: Use `ddev drush uli` to access the site
6. **Check logs** if tests fail: `ddev logs` and `ddev drush wd-show`
7. **Use snapshots** for quick rollbacks: `ddev snapshot` before major changes, `ddev snapshot restore` to revert

### Pre-Completion Checklist

Before marking work complete, ensure:

- [ ] All tests pass: `ddev phpunit`
- [ ] Code standards met: `ddev phpcs` (no errors)
- [ ] Static analysis passes: `ddev phpstan` (no errors)
- [ ] Caches cleared: `ddev drush cr`
- [ ] Manual testing done (if UI changes)
- [ ] Webprofiler frontend verified (if relevant): toolbar displays, data collectors work
- [ ] No unintended files in commit

### Environment Details

The GitHub Copilot agent environment includes:

- **DDEV**: Local development environment with Docker
- **Drupal**: Version 11+ (PHP 8.3+)
- **Database**: MariaDB 10.6
- **Testing**: PHPUnit, Selenium Chrome (for browser tests)
- **Code Quality**: PHPStan, PHP_CodeSniffer
- **Addons**: ddev-drupal-contrib, ddev-selenium-standalone-chrome

### Quick Debugging

If something doesn't work:

```bash
# 1. Check project status
ddev describe

# 2. Restart DDEV
ddev restart

# 3. Clear Drupal caches
ddev drush cr

# 4. Check logs
ddev logs

# 5. Verify module is enabled
ddev drush pml | grep webprofiler

# 6. Reinstall if needed
ddev drush site:install -y
ddev drush en webprofiler -y
```

For detailed instructions, see the specific documentation files linked above.
