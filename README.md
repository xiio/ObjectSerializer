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

    public function setUser(User $user)
    {
        $this->user = $user;
    }
}

class Item
{
    private $name;

    /**
     * @var ItemPrice
     */
    public $price;

    /**
     * Item constructor.
     *
     * @param $name
     * @param \ItemPrice $price
     */
    public function __construct($name, ItemPrice $price)
    {
        $this->name = $name;
        $this->price = $price;
    }
}

class ItemPrice
{
    private $value;
    private $currency;

    public function __construct($value, $currency)
    {
        $this->value = $value;
        $this->currency = $currency;
    }
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
You can use filters to manipulate data. You can use factory to use built in filter or create your own by implement `FilterInterface`.
Assume we have following code

```php
$serializer = new ObjectSerializer();
$order = new Order("ASDF123", 123, ['item1', 'item2']);
$order->setUser(new User('dev@email'));
```

#### Blacklist filter example
```php
$blacklist = FilterFactory::blacklist(Order::class);
$blacklist->addField('items'); //field items will be removed

$data = $serializer->serializeToJson($order, $blacklist);
echo $data; // {"id":"ASDF123","sum":123,"user":{"email":"dev@email"}}
```

#### Whitelist filter example
```php
$whitelist = FilterFactory::whitelist(Order::class);
$whitelist->addField('user'); //leave field user. Remove the rest.

$data = $serializer->serializeToJson($order, $whitelist);
echo $data; // {"user":{"email":"dev@email"}}
```

#### Callback filter example
```php
$filter = function(array $orderData) {
    unset($orderData['items']);
    $orderData['sum'] = 0;

    return $orderData;
};
$callbackFilter = FilterFactory::callback(Order::class, $filter);

$data = $serializer->serializeToJson($order, $callbackFilter);
echo $data; // {"id":"ASDF123","sum":0,"user":{"email":"dev@email"}}
```

### Mappings
You can use mapping to define custom properties type during deserialization. 
There is two types right now:
* mapClass - map property as an instance of class
* mapArray - map property as array of objects

```php
$serializer = new ObjectSerializer();

$item1 = new Item('item1', new ItemPrice(22, 'USD'));
$item2 = new Item('item2', new ItemPrice(11, 'EUR'));
$order = new Order("ASDF123", 123, [
    $item1,
    $item2,
]);
$order->setUser(new User('dev@email'));
$data = $serializer->serializeToJson($order);
echo $data;
//{
//  "id": "ASDF123",
//  "sum": 123,
//  "items": [
//    {
//      "name": "item1",
//      "price": {
//        "value": 22,
//        "currency": "USD"
//      }
//    },
//    {
//      "name": "item2",
//      "price": {
//        "value": 11,
//        "currency": "EUR"
//      }
//    }
//  ],
//  "user": {
//    "email": "dev@email"
//  }
//}


$mapping = MappingFactory::createObjectMapping(Order::class);
$mapping->mapClass('user', User::class);
$mapping->mapArray('items', Item::class);
$mapping->mapClass('items.price', ItemPrice::class);

$recoveredOrder = $serializer->deserializeJson($data, $mapping);
var_dump($recoveredOrder->user); // is object "User"
var_dump($recoveredOrder->items); // is array of objects "Item"
var_dump($recoveredOrder->items[0]->price); // is object "ItemPrice"
```

## Test
Unit testing is provided by phpspec.
`composer test` or `bin/phpspec run`

## License
MIT

## TODO
* Mapping property names.
* Support serialize array of objects

## Known issues
* cannot serialize array of objects
