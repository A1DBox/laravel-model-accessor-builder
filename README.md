# Laravel Model Accessor Builder

[![Latest Version on Packagist](https://img.shields.io/packagist/v/a1dbox/laravel-model-accessor-builder.svg?style=flat-square)](https://packagist.org/packages/a1dbox/laravel-model-accessor-builder)
[![Total Downloads](https://img.shields.io/packagist/dt/a1dbox/laravel-model-accessor-builder.svg?style=flat-square)](https://packagist.org/packages/a1dbox/laravel-model-accessor-builder)

### You can build query on model accessor. Sort or filter by accessor, and at the same time it's a regular working accessor

## Installation

You can install the package via composer:

```bash
composer require a1dbox/laravel-model-accessor-builder
```

## Usage

Use the `HasAccessorBuilder` trait in your model to provide work of accessor builder:

```php
use A1DBox\Laravel\ModelAccessorBuilder\Concerns\HasAccessorBuilder;

class User extends Model
{
    use HasAccessorBuilder;
}
```

Create Accessor for attribute

###### _Laravel 8.x accessor defining style_
```php
class User extends Model
{
    use HasAccessorBuilder;
    
    public function getFullNameAttribute()
    {
        return AccessorBuilder::make(
            $this,
            fn (AccessorBuilder\BlueprintCabinet $cabinet) => $cabinet->trim(
                $cabinet->concat(
                    $cabinet->col('name'),
                    $cabinet->str(' '),
                    $cabinet->col('last_name'),
                )
            ),
        );
    }
}
```

As example, code above will do same as this one, when resolving accessor value:

```php
return trim($this->name . ' ' . $this->last_name);
```

### Example #1

Here, the `full_name` attribute contained in model `$attributes` after query
And  when using accessor `->full_name`, the value will be taken from `$attributes`

```php
$user = User::query()
    ->withAccessor('full_name')
    ->find(1);

echo $user->full_name; //John Doe
```
SQL Query executed:
```sql
SELECT
    *,
    trim(concat(name, ' ', last_name)) AS full_name
FROM users WHERE id = 1
```

### Example #2

Here, the `full_name` attribute NOT contained in `$attributes` variable of model, and
when using accessor `$user->full_name`, the value will be built from model `$attributes`

```php
$user = User::find(1);

echo $user->full_name; //John Doe
```
SQL Query executed:
```sql
SELECT * FROM users WHERE id = 1
```

### Example #3

Get all users ordered by `full_name`

```php
$users = User::query()
    ->withAccessor('full_name')
    ->orderBy('full_name')
    ->get();
```

### Example #4

Filter users by `full_name`

```php
$users = User::query()
    ->withAccessor('full_name')
    ->where('full_name', 'John Doe')
    ->get();
```

### You can pass array to `withAccessor` method

```php
$users = User::query()
    ->withAccessor(['full_name', 'full_address'])
    ->get();
```


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/A1DBox/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [A1DBox](https://github.com/A1DBox)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
