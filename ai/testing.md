# Testing Guidelines

## Overview

Always test changes before marking work complete. This project uses multiple testing approaches to ensure quality.

## Test Types

### PHPUnit Tests

Located in `tests/` directory:

- `tests/src/Unit/`: Unit tests (fast, no Drupal bootstrap)
- `tests/src/Kernel/`: Kernel tests (minimal Drupal bootstrap)
- `tests/src/Functional/`: Functional tests (full Drupal installation)
- `tests/src/FunctionalJavascript/`: Browser tests with JavaScript

### Running Tests

```bash
# Run all tests
ddev phpunit

# Run specific test suite
ddev phpunit --testsuite=unit
ddev phpunit --testsuite=kernel
ddev phpunit --testsuite=functional

# Run single test file
ddev phpunit tests/src/Unit/SomeTest.php

# Run single test method
ddev phpunit --filter testMethodName

# Run with verbose output
ddev phpunit --verbose
```

## Browser Testing

Selenium Chrome addon is pre-configured for browser testing:

```bash
# Run Nightwatch tests (if configured)
ddev nightwatch

# Check Selenium container
ddev describe
ddev logs -s selenium-chrome
```

## Manual Testing

For UI or functional changes:

```bash
# Get one-time login link
ddev drush uli

# Access the site
ddev launch

# Clear caches after changes
ddev drush cr

# Check module status
ddev drush pml | grep webprofiler
```

### Manual Testing Checklist

1. Enable the module: `ddev drush en webprofiler -y`
2. Access admin UI: Visit `/admin/config/development/devel/webprofiler`
3. Configure collectors as needed
4. Test profiling on various pages
5. Check toolbar display
6. Verify dashboard functionality

## Module-Specific Commands

```bash
# Export database query data
ddev drush webprofiler:export-database-data <token> /tmp

# Check webprofiler commands
ddev drush list --filter=webprofiler
```

## Testing Workflow

### For New Features

1. Create/modify necessary files in `src/`
2. Write tests in `tests/src/`
3. Run tests: `ddev phpunit`
4. Check code style: `ddev phpcs`
5. Run static analysis: `ddev phpstan`
6. Test manually if UI is involved

### For Bug Fixes

1. Write a test that reproduces the bug (if possible)
2. Fix the bug
3. Verify the test passes: `ddev phpunit`
4. Run full test suite to ensure no regressions
5. Check code quality

## Before Completing Work

Always perform these checks:

1. ✅ Run the test suite: `ddev phpunit`
2. ✅ Check coding standards: `ddev phpcs`
3. ✅ Run static analysis: `ddev phpstan`
4. ✅ Clear caches: `ddev drush cr`
5. ✅ Test manually if UI changes were made
6. ✅ Verify no unintended files are being committed

## Debugging Tests

```bash
# Run tests with verbose output
ddev phpunit --verbose

# Check for errors in logs
ddev drush wd-show --severity=Error

# Clear test caches
ddev drush cr

# Reinstall site for clean state
ddev drush site:install -y
ddev drush en webprofiler -y
```

## Test Environment Notes

- Tests run in isolated environment with clean database
- Use `SIMPLETEST_DB` for database connection
- Browser tests use Selenium Chrome container
- Test output goes to `/tmp` by default
- All tests should be idempotent and independent
