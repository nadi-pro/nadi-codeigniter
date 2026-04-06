# Nadi CodeIgniter SDK

Nadi monitoring SDK for CodeIgniter 4 applications. Captures exceptions, slow queries, and HTTP errors for the [Nadi](https://nadi.pro) monitoring platform.

## Requirements

- PHP 8.1+
- CodeIgniter 4.3+

## Installation

```bash
composer require nadi-pro/nadi-codeigniter
```

## Configuration

Register the service in `app/Config/Services.php` or call in your bootstrap:

```php
\Nadi\CodeIgniter\NadiService::register();
```

Add the filter in `app/Config/Filters.php`:

```php
public array $globals = [
    'after' => [
        \Nadi\CodeIgniter\Filters\NadiFilter::class,
    ],
];
```

Copy the config class to `app/Config/Nadi.php` or set environment variables:

```
NADI_ENABLED=true
NADI_DRIVER=log
NADI_API_KEY=your-api-key
NADI_APP_KEY=your-app-key
```

## Console Commands

```bash
php spark nadi:install          # Install Nadi
php spark nadi:test             # Test connectivity
php spark nadi:verify           # Verify configuration
php spark nadi:update-shipper   # Update shipper binary
```

## Security & Data Privacy

> **Important:** Nadi captures and transmits application error data including
> exception messages, stack traces, SQL queries, HTTP request details, and
> custom content. This data may contain Personally Identifiable Information (PII).

**As the consumer, you are responsible for:**

- Sanitizing or redacting PII from entry content
- Filtering sensitive HTTP headers (e.g., `Authorization`, `Cookie`) from captured data
- Ensuring compliance with your organization's data handling policies (GDPR, HIPAA, SOC2, etc.)
- Using HTTPS endpoints for all drivers in production environments

See [SECURITY.md](SECURITY.md) for vulnerability reporting and security considerations.

## License

MIT
