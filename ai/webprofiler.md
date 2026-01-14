# Webprofiler Module Configuration and Testing

## Overview

This guide covers webprofiler-specific configuration and frontend verification for the Webprofiler Drupal module.

## Webprofiler-Specific Configuration

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

## Verifying Webprofiler Frontend

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

## Pre-Completion Checklist

Before marking work complete, ensure:

- [ ] All tests pass: `ddev phpunit`
- [ ] Code standards met: `ddev phpcs` (no errors)
- [ ] Static analysis passes: `ddev phpstan` (no errors)
- [ ] Caches cleared: `ddev drush cr`
- [ ] Manual testing done (if UI changes)
- [ ] Webprofiler frontend verified (if relevant): toolbar displays, data collectors work
- [ ] No unintended files in commit

## Important Configuration Notes

### Enabling Time Metrics

To enable the collection of time metrics, you need to add this line to the `settings.php` file:

```php
$settings['tracer_plugin'] = \Drupal\webprofiler\Plugin\Tracer\StopwatchTracer::class;
```

### Disabling Custom Error Handler

WebProfiler uses a custom error handler. If you need to deactivate it, add to `settings.php`:

```php
$settings['webprofiler_error_page_disabled'] = TRUE;
```

Remember to clear the cache after making settings changes.

### Webprofiler Settings UI

After enabling the module, only some widgets are displayed by default. You can enable all collectors on the WebProfiler settings page at:

`/admin/config/development/devel/webprofiler`

## Performance Considerations

Remember that Webprofiler:
- Has performance overhead
- Instruments Drupal internals for profiling
- Should never be used in production
- May affect test execution time

When making changes, consider the performance impact and document any significant changes.
