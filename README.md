[![Latest Stable Version](https://poser.pugx.org/infrajs/once/v/stable)](https://packagist.org/packages/infrajs/once) [![Total Downloads](https://poser.pugx.org/infrajs/once/downloads)](https://packagist.org/packages/infrajs/once)

# once
Excecute php one time ```infra_once()```

# Data caching
>caches data for fast access.

```php
$data = Once::exec('unique_name', $data [, array $args [, boolean $re]] );
// unique_name - A unique name in text format.
// $data - The anonymous function returns the correct data to cache.
// $args - An array of arguments which can be function called $data. When passing different arguments caching is not happening.
// $re - If this parameter is passed as true, the caching is not happening.
Once::clear('unique_name'); 
// Clears the cache for the unique name.
```

### Testing

##### Testing run the file test.php:

> positive answer

```
{result:1}
```

> negative answer

```
{result:0, msg:"В работе методов класса Once произошел сбой."}
```

##### Testing with PHPunit

```
phpunit --bootstrap Once.php tests/OnceTest
```

# Кэширование данных

> кэширует данные для более быстрого доступа к одним и тем же обращениям

```php
$data = Once::exec('unique_name', $data [, array $args [, boolean $re]] );
// unique_name - уникальное имя в текстовом формате.
// $data - анонимная функция возвращающая необходимые данные для кэширования.
// $args - массив аргументов с которыми может быть вызвана функция $data. При передаче разных аргументов кэширование не происходит.
// $re - если данный параметр передан как true, кэширование не происходит.
Once::clear('unique_name'); 
// Очищает кэш для уникального имени.
```

### Тест

##### Для тестирования откройте в браузере test.php:

> при положительном ответе вы увидете следующее сообщение

```
{result:1}
```

> если в работе кода произойдет сбой, то сообщение будет

```
{result:0, msg:"В работе методов класса Once произошел сбой."}
```

##### Для тестирование с помощью PHPunit

```
phpunit --bootstrap Once.php tests/OnceTest
```

