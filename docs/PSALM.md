# Psalm Configuration

This project uses [Psalm](https://psalm.dev/) for advanced static analysis at error level 8.

## Running Psalm

Run static analysis:

```bash
composer psalm
```

Or use Psalm directly:

```bash
vendor/bin/psalm
```

Show all issues including info-level:

```bash
vendor/bin/psalm --show-info=true
```

## Configuration

Psalm is configured via `psalm.xml` with the following settings:

- **Error Level**: 8 (strict)
- **Analyzed Paths**: `src/` directory
- **Type Coverage**: Tracks percentage of code with type information
- **Unused Code Detection**: Disabled (can be enabled for cleanup)
- **Unused Baseline Detection**: Enabled

### Issue Handlers

The configuration suppresses some issues that are expected in CakePHP applications:

1. **UndefinedMagicPropertyFetch** - CakePHP components (`$this->Authentication`, `$this->Flash`) are loaded via magic methods
2. **MissingPropertyType** - Entity properties are defined at runtime
3. **UndefinedPropertyFetch** - EntityInterface properties are dynamic
4. **UndefinedFunction** - CakePHP's `__()` translation function is loaded at runtime

These are standard patterns in CakePHP and don't represent actual errors.

## Generating Baseline

If you need to add Psalm to an existing project with many issues, generate a baseline:

```bash
composer psalm-baseline
```

This creates `psalm-baseline.xml` which allows you to gradually fix issues without blocking CI.

## Type Coverage

Psalm tracks how much of your code has type information:

```bash
# View type coverage report
vendor/bin/psalm --show-info=true --stats
```

Current project type coverage: **~75.5%**

## Comparison with PHPStan

Both tools are used in this project for comprehensive static analysis:

| Feature | PHPStan | Psalm |
|---------|---------|-------|
| **Focus** | Type safety | Type inference & security |
| **Strengths** | Clear error messages | Better type inference |
| **Dead Code** | Limited | Excellent |
| **IDE Integration** | Excellent | Good |
| **Learning Curve** | Easier | Steeper |

Using both tools provides the most comprehensive static analysis coverage.

## CI Integration

Add Psalm to your continuous integration:

```yaml
# GitHub Actions example
- name: Psalm Analysis
  run: composer psalm
```

```yaml
# GitLab CI example
psalm:
  script:
    - composer install
    - composer psalm
```

## IDE Integration

### PHPStorm
1. Go to Settings → PHP → Quality Tools → Psalm
2. Point to `vendor/bin/psalm`
3. Enable automatic inspection

### VS Code
Install the Psalm extension:
```bash
code --install-extension getpsalm.psalm-vscode-plugin
```

## Error Levels

Psalm has 9 error levels (1-8, with max):

- **Level 1**: Basic issues
- **Level 3**: Medium issues
- **Level 5**: Stricter checks
- **Level 8**: Very strict (used here)
- **Level max**: Strictest possible

## Common Issues and Solutions

### Out of Memory

If Psalm runs out of memory:

```bash
# Increase memory limit
php -d memory_limit=1G vendor/bin/psalm
```

### Clearing Cache

If you encounter strange errors:

```bash
vendor/bin/psalm --clear-cache
```

### Suppressing Individual Issues

Use `@psalm-suppress` annotation:

```php
/** @psalm-suppress UndefinedMethod */
$result = $this->someMethod();
```

### Type Assertions

Help Psalm understand your code better:

```php
/** @var User $user */
$user = $this->Users->get($id);

// Or using assert
assert($user instanceof User);
```

## Psalm Plugins

Consider adding CakePHP-specific plugins:

```bash
composer require --dev psalm/plugin-cakephp
vendor/bin/psalm-plugin enable psalm/plugin-cakephp
```

## Benefits

Psalm provides:
- Advanced type inference
- Security issue detection
- Dead code detection
- Template type checking
- Better generic support than PHPStan
- Taint analysis for security
- Mutation testing integration

## More Information

- [Psalm Documentation](https://psalm.dev/docs/)
- [Fixing Issues](https://psalm.dev/docs/running_psalm/issues/)
- [Type System](https://psalm.dev/docs/annotating_code/type_syntax/atomic_types/)
- [Configuration Reference](https://psalm.dev/docs/running_psalm/configuration/)
