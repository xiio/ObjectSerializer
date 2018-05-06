!!! WARNING. THIS LIBRARY IS UNDER DEVELOPMENT. DO NOT USE IT IN PRODUCTION ENV.

# ObjectSerializer
Object serializer allows to serialize/deserialize objects including public protected and private properties without 
using reflection.

## Features
* Serializing object to with private/protected/public properties.
* Supported formats: array, json.
* Allow filtering properties by name. Choose what to serialize.
* Recovering object state by deserializng data with access to private/protected/public properties.
* Type mapping support.

## Usage
### Fixture
Assume we have this silly implementation

```php
class Order
{
    private $id;
    private $sum;
    public $items;
    public $user;

    public function __construct($id, int $sum, array $items = [])
    {
        $this->id = $id;
        $this->sum = $sum;
        $this->items = $items;
    }

    public function setUser(User $user) {
        $this->user = $user;
    }
}

class Item
{
    private $name;
}

class User
{
    private $email;

    public function __construct($email)
    {
        $this->email = $email;
    }
}
```

### Simple usage
```php
$serializer = new ObjectSerializer();
$order = new Order("ASDF123", 123, ['item1', 'item2']);
$data = $serializer->serializeToJson($order);
echo $data; // {"id":"ASDF123","sum":123,"items":[{"name":"item1"},{"name":"item2"}],"user":null}

$recoveredOrder = $serializer->deserializeJson($data, Order::class);
var_dump($order == $recoveredOrder); // true
```

### Filter
You can use filters to decide with fields will be omitted during serialization.
There is two filters avaliable right now:
* PropertyFilter - define properties to omit 
* CallbackFilter - use custom callback to make decision

```php
$serializer = new ObjectSerializer();
$order = new Order("ASDF123", 123, ['item1', 'item2']);

$filter = new PropertyFilter();
$filter->setField('items');

$data = $serializer->serializeToJson($order, $filter);
echo $data; // {"id":"ASDF123","sum":123,"user":null}
```

### Mappings
You can use mapping to define custom properties type during deserialization. 
There is two types right now:
* mapClass - map property as an instance of class
* mapArray - map property as array of objects

```php
$serializer = new ObjectSerializer();
$order = new Order("ASDF123", 123, [['name' => 'item1'], ['name' => 'item2']]);
$order->setUser(new User('dev@email'));
$data = $serializer->serializeToJson($order);
echo $data; // {"id":"ASDF123","sum":123,"items":[{"name":"item1"},{"name":"item2"}],"user":{"email":"dev@email"}}

$mapper = new PropertyMapper();
$mapper->mapClass('user', User::class);
$mapper->mapArray('items', Item::class);

$recoveredOrder = $serializer->deserializeJson($data, Order::class, $mapper);
var_dump($recoveredOrder->items); // is array of objects "Item"
var_dump($recoveredOrder->user); // is array of objects "Item"

//    array(2) {
//      [0]=>
//      object(Item)#20 (1) {
//        ["name":"Item":private]=>
//        string(5) "item1"
//      }
//      [1]=>
//      object(Item)#23 (1) {
//        ["name":"Item":private]=>
//        string(5) "item2"
//      }
//    }
//    object(User)#26 (1) {
//      ["email":"User":private]=>
//      string(9) "dev@email"
//    }
```

## Test
Unit testing is provided by phpspec.
`composer test` or `bin/phpspec run`

## License
MIT

## TODO
* Mapping using dot notation for deep nested properties.
* Mapping property names.
* Filtering properties by name using wildard.

## Known issues
* not yet possible to set mapping for deep nested properties. (\>2 nesting lvl)
