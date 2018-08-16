[![Latest Stable Version](https://poser.pugx.org/infrajs/once/v/stable)](https://packagist.org/packages/infrajs/once) [![Total Downloads](https://poser.pugx.org/infrajs/once/downloads)](https://packagist.org/packages/infrajs/once)

# Кэширование данных

> кэширует данные для более быстрого доступа к одним и тем же обращениям

```php
$data = Once::exec('unique_name', $fn, $args = [], $condfn = [], $condargs = [], $level = 0);
// unique_name - уникальное имя в текстовом формате.
// $fn - анонимная функция возвращающая необходимые данные для кэширования.
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

