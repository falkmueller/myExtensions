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
    use myExtensions\myHook;

    $hooks = myHook::Instance();

    /* add a filter */
    $hooks->addFilter("testfilter", function($value){ return "filter-".$value;});

    /* call a filter */
    echo $hooks->filter("testfilter", 1);//filter-1
    echo "<br/>";

    /* add middleware (callable, anonymouse function) */
    $hooks->addMiddleware("m", function($res, $next){ 
        $res .= " M1-before "; 
        $res = $next($res);
        $res .= " M1-after ";

        return $res;

    });

    class x {

        static function y ($res, callable $next,$z1, $z2){
            $res .= " M2-bevore ";
            $res = $next($res);   
            $res .= " M2-after ";

            return $res;
        }

        public function call_me($z1, $z2, $res = ''){
            $res .= " core ";
            return $res;
        }

    }

    * add middleware (callable, static function)*/
    $hooks->addMiddleware("m", "x::y");

    * call middleware (callable, object function)*/
    $x = new x();
    $res = $hooks->middleware("m", array($x, "call_me"),"", 1, 2);

    echo $res; //M1-before M2-bevore core M2-after M1-after
```

# mySession
```php
    echo \myExtensions\mySession::Instance()->get("name", "default");
    \myExtensions\mySession::Instance()->put("name", "falk");
    echo \myExtensions\mySession::Instance()->get("name");
```

# myTemplate
```php
    $t = new \myExtensions\myTemplate();
    $t->addFunction("testfunction", function($value){ return "TestFunction: {$value}"; });
    $t->setSetting("dir", __dir__.'/templates/');

    echo $t->render("index.phtml", array("name" => "myExtension"));
```

templates/index.phtml
```php
    <?php $this->extend("layout.phtml");?>

    <?php $this->startBlock("content", "APPEND") ?>
        Dies ist ein Test: <?php echo $this->name ?><br/>
        <?php echo $this->testfunction("value"); ?>
    <?php $this->endBlock("content") ?>
```

templates/layout.phtml
```php
    <html>
        <header>

        </header>
        <body>
            HEADER

            <div>
                <?php $this->startBlock("content") ?>
                Dies ist der Content
                <?php $this->endBlock("content") ?>
            </div>

            <?php $this->insert("footer.phtml"); ?>
        </body>
    </html>
```
