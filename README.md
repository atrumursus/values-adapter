# values-adapter

The library allows you to verify and convert variables of various types. It can be useful for working with data in REST requests, or when analyzing data coming from outside.

## Sample 1 :
```
(new VInt())->min(1)->max(11)->convert('10')
	equal 10

(new VBool())->convert('on')
	equal true


(new VDateTime())->outFormat(\DateTime::ATOM)->inFormat(\DateTime::RFC850)->convert("Sunday, 17-Nov-24 18:56:48 UTC")
	equal "2024-11-17T18:56:48+00:00"

```

## Sample 2 :
```
input_data = [
	'employee' => [
		'name' => '  JAMES',
		'lastname' => '   BOnd   ',
		'additional' => [
			'age' => '27',
			'gender' => 'man'
		]
	]
];
```

Prepared adapter:
```
$adapter = (new VDict())
	->src('employee')
	->map([
		'NAME' => [
			'src' => 'name',
			'adapter' => (new VString())->case('title')->trim(true)
		],
		'LAST_NAME' => [
			'src' => 'lastname',
			'adapter' => (new VString())->case('title')->trim(true)
		],
		'FULL_NAME' => [
			'extractor' => function ($data, $options) {
				$adapter = (new VExistString)->default('')->trim(true);
				return $adapter->convert($data['name']) . ' ' . $adapter->default('Anonymous')->convert($data['lastname']);
			},
			'adapter' => (new VString())->case('title')->trim(true)
		],
		'AGE' => [
			'src' => ['additional', 'age'],
			'adapter' => (new VInt())->min(16)
		]
	]);
```
Convert:
```
$result = $adapter->convert($this->inputValue1);
```

Result:
```
[
	'NAME' => 'James',
	'LAST_NAME' => 'Bond',
	'FULL_NAME' => 'James Bond',
	'AGE' => 27
];
```