# once
Excecute php one time ```infra_once()```
[![Latest Stable Version](https://poser.pugx.org/infrajs/once/v/stable)](https://packagist.org/packages/infrajs/once) [![Total Downloads](https://poser.pugx.org/infrajs/once/downloads)](https://packagist.org/packages/infrajs/once)

# Data caching
>caches data for fast access.

```php
$data = Once::exec('unique_name', $data); //$data - массив данных для кэширования
Once::clear('unique_name'); // Очищает кэш для этих данных
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

