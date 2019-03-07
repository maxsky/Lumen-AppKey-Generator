# Install

```bash
composer require maxsky/lumen-app-key-generator
```


# Description

Support generate **APP_KEY** in Lumen same as Laravel.


# Usage

Modify `$commands` variable in `app/Console/Kernel`:

```php
protected $commands = [
    \Illuminate\Console\KeyGenerateCommand::class,
];
```

Then run command at project root path:

```bash
php artisan key:generate
php artisan key:generate --show
```

Add param `--show` will display a generate key without replace **APP_KEY** in `.env` file.

