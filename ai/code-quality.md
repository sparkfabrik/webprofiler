# Code Quality Standards

## Overview

This project follows Drupal coding standards and uses automated tools to enforce quality.

## Coding Standards

### Drupal Standards

This project follows the [Drupal Coding Standards](https://www.drupal.org/docs/develop/standards):

- Use Drupal coding standards for PHP
- Follow Drupal documentation standards
- Use proper type hints and return types (PHP 8.3+)
- Write clear, self-documenting code

### Code Style Tools

#### PHP_CodeSniffer

Check for coding standard violations:

```bash
# Check all code
ddev phpcs

# Check specific file
ddev phpcs src/SomeFile.php

# Check with detailed output
ddev phpcs -v
```

Fix coding standard issues automatically:

```bash
# Fix all auto-fixable issues
ddev phpcbf

# Fix specific file
ddev phpcbf src/SomeFile.php
```

Configuration: `phpcs.xml.dist`

#### PHPStan

Run static analysis to catch bugs and type issues:

```bash
# Run PHPStan
ddev phpstan

# Run with verbose output
ddev phpstan -v

# Analyze specific file
ddev phpstan analyze src/SomeFile.php
```

Configuration: `phpstan.neon`

## Code Quality Workflow

### Before Committing

Always run these checks:

```bash
# 1. Check coding standards
ddev phpcs

# 2. Fix auto-fixable issues
ddev phpcbf

# 3. Run static analysis
ddev phpstan

# 4. Run tests
ddev phpunit
```

### When Making Changes

1. Write code following Drupal standards
2. Add/update tests as needed
3. Run `ddev phpcs` - fix any violations
4. Run `ddev phpstan` - address any issues
5. Run `ddev phpunit` - ensure tests pass
6. Commit only when all checks pass

## Common Issues and Fixes

### Coding Standards Violations

Most common violations:

- Missing docblocks
- Incorrect indentation
- Line length > 80 characters
- Missing type hints

Many can be auto-fixed with `ddev phpcbf`.

### PHPStan Issues

Common issues:

- Missing type hints
- Undefined variables
- Incorrect return types
- Accessing undefined properties

These require manual fixes - read the error message carefully.

## Code Comments

### When to Add Comments

- Complex logic that isn't self-evident
- Workarounds or non-obvious solutions
- Public API methods (required)
- Class and interface definitions (required)

### When NOT to Add Comments

- Don't add comments unless they match existing style
- Don't comment obvious code
- Don't leave commented-out code
- Don't add TODO comments without issue references

### Docblock Standards

All public methods must have docblocks:

```php
/**
 * Brief description of what the method does.
 *
 * Longer description if needed, explaining the purpose,
 * behavior, and any important details.
 *
 * @param string $param1
 *   Description of parameter.
 * @param int $param2
 *   Description of parameter.
 *
 * @return bool
 *   Description of return value.
 *
 * @throws \Exception
 *   Description of when exception is thrown.
 */
public function exampleMethod(string $param1, int $param2): bool {
  // Implementation
}
```

## File Structure

### Source Code

- `src/`: Main source code
- `tests/`: Test files
- `config/`: Configuration files
- `templates/`: Twig templates
- `js/`: JavaScript files
- `css/`: CSS files

### Naming Conventions

- Classes: PascalCase (e.g., `MyClassName`)
- Methods/Functions: camelCase (e.g., `myMethodName`)
- Variables: camelCase or snake_case (e.g., `$myVariable` or `$my_variable`)
- Constants: SCREAMING_SNAKE_CASE (e.g., `MY_CONSTANT`)

## Dependencies

### Adding New Dependencies

Before adding dependencies:

1. Check if functionality exists in Drupal core
2. Verify the dependency is actively maintained
3. Check for security issues
4. Use stable versions only

Update composer:

```bash
ddev composer require vendor/package

# For dev dependencies
ddev composer require --dev vendor/package
```

### Updating Dependencies

```bash
# Update all dependencies
ddev composer update

# Update specific package
ddev composer update vendor/package

# Always test after updates
ddev drush cr
ddev drush updb -y
ddev phpunit
```

## Performance Considerations

Remember that Webprofiler:

- Has performance overhead
- Instruments Drupal internals
- Should never be used in production
- May affect test execution time

When making changes:

- Consider performance impact
- Don't add unnecessary overhead
- Profile changes if needed
- Document performance implications
