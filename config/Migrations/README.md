# Database Migrations

This directory contains database migrations for the Brammo/Auth plugin.

## Available Migrations

### 20231101000000_CreateUsers.php

Creates the `users` table with the following columns:
- `id` - Integer, Primary Key, Auto Increment
- `name` - String(255), Nullable
- `email` - String(255), Not Null, Unique Index
- `password` - String(255), Not Null
- `created` - DateTime, Nullable
- `modified` - DateTime, Nullable

## Running Migrations

To run the migrations for this plugin:

```bash
# From your application root
bin/cake migrations migrate -p Brammo/Auth
```

To check migration status:

```bash
bin/cake migrations status -p Brammo/Auth
```

To rollback the last migration:

```bash
bin/cake migrations rollback -p Brammo/Auth
```

## Seeds

Sample data seeds are available in the `config/Seeds` directory.

### UsersSeed

Creates two sample users:
- Admin User (admin@example.com / admin123)
- Test User (user@example.com / password)

To seed the database:

```bash
bin/cake migrations seed -p Brammo/Auth --seed UsersSeed
```

**Note**: Seeds are intended for development and testing environments only. Do not use in production.

## Creating New Migrations

If you need to add columns or modify the users table:

```bash
# Generate a new migration
bin/cake bake migration AddFieldToUsers field_name:string -p Brammo/Auth

# Run the migration
bin/cake migrations migrate -p Brammo/Auth
```

## Migration Best Practices

1. **Never modify existing migrations** - Always create new migrations for changes
2. **Test migrations** - Run migrations in development before deploying
3. **Backup data** - Always backup production data before running migrations
4. **Use transactions** - Migrations automatically use transactions when possible
5. **Version control** - Always commit migration files to your repository

## Troubleshooting

### Migration Already Applied

If you see "Migration already applied", check the status:

```bash
bin/cake migrations status -p Brammo/Auth
```

### Rolling Back

To completely reset and re-run migrations:

```bash
bin/cake migrations rollback -p Brammo/Auth -t 0
bin/cake migrations migrate -p Brammo/Auth
```

### Custom Database Connection

To use a different database connection:

```bash
bin/cake migrations migrate -p Brammo/Auth -c custom_connection
```

## More Information

For more details on CakePHP migrations, visit:
https://book.cakephp.org/migrations/4/en/index.html
