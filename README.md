# myExtensions
## simple PHP Classes

### Install over composer
composer.json
```js
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/falkmueller/myExtensions.git"
        }
    ],
    "require": {
        "falkm/myExtensions": "dev-develop"
    }
}
```

# mydb
## PHP PDO MySql database wrapper

### Use

Create Connection
```php
    $db = new \myExtensions\mydb("localhost", "test", "root", "");
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

# myEntity
first, set global myDb-Instance for Entity-Model
```php
\myExtensions\myEntity::$db = $db;
```

create entitys
```php
class user extends \myExtensions\myEntity {
    

    protected static $_table = "user";
    
    public static function fields(){
        return array(
            "id" => array('type' => 'integer', 'primary' => true, 'autoincrement' => true),
            "name" => array('type' => 'string', "default" => ""),
            "password" => array('type' => 'string', "default" => ""),
        );
    }
}
```

work with entity
```php
    $user1 = \app\entity\user::selectOne(array("id" => 1));
    $new = new \app\entity\user(array("id" => 1, "name" => "mueller"));
    if($new->exists()){
        echo "exists";
    } else {
        $new->insert();
    }

    $new->name = "falk";
    $new->update();

    echo json_encode($a->toArray());
```

# myHook
```php
    \myExtensions\myHook::Instance()->->registrate("MvcRouterCall:filter", function($instance, $returnvalue, $data){
            $returnvalue["action"] = "index";
            return $returnvalue;
        });
```

```php
    \myExtensions\myHook::Instance()->->registrate("MvcRouterCall:filter", array($this, 'functionname'));
```

```php
    $routerArguments = \myExtensions\myHook::Instance()->filter("MvcRouterCall:filter", $this, $routerArguments);
```

```php
    \myExtensions\myHook::Instance()->notify("MvcRouterCall:before", $this, $routerArguments);
```

# mySession
```php
    echo \myExtensions\mySession::Instance()->get("name", "default");
    \myExtensions\mySession::Instance()->put("name", "falk");
    echo \myExtensions\mySession::Instance()->get("name");
```


