# mydb
## PHP PDO MySql database wrapper

### Install over composer
composer.json
```js
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/falkmueller/databass_class.git"
        }
    ],
    "require": {
        "falkm/mydb": "dev-develop"
    }
}
```

### Use

Create Connection
```php
    $db = new \mydb\mydb("localhost", "test", "root", "");
```

Insert Statement with Parameter
```php
    $statement = $db->createQuery("INSERT INTO testtbl (test) values (:name)", array(":name" => "falk"));
    $statement->execute();
```

Select Statement with multible parameter
```php
    $statement = $db->createQuery("SELECT * FROM testtbl where test in (:name)", array(":name" => array("falk3", "falk2")));
    print_r($statement->fetchAll());
```
