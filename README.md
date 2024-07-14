# sweikenb/dirty

Library for checking if an object or array has changes (is dirty) since the last check.

License: MIT

## Installation

```bash
composer require sweikenb/dirty
```

If you plan to use this library in a **Symfony** project, consider checking out the corresponding
[DirtyBundle](https://github.com/sweikenb/dirty-bundle) instead.

## Usage

**How does it work?**

In order to check if the test-subject has untracked changes, the given object or array will be normalized, flattened and
stored using a configurable storage adapter.

The next time the check is executed the current data will be compared to the data of the previous check.
Which of the subjects fields will be tracked or ignored can be [configured](#configuration) (see below).

**Usage:**

As the result of the check, you will receive a detailed list of fields that changed and the corresponding previous and
current value:

```php
$categoryId = 'category:123';
$categoryData = [
    'title' => 'My Category', 
    'tags' => ['Foo', 'Bar', 'Baz'],
    'createdAt' => '2024-07-10 15:31:00' 
];

$service = new \Sweikenb\Library\Dirty\Service\DirtyCheckService();

$result = $service->execute($categoryId, $categoryData);

if ($result->isDirty) {
    foreach ($result->diffs as $fieldPath => $diff) {
        echo sprintf("Field '%s' is dirty! '%s' -> '%s'\n", $fieldPath, $diff->previously, $diff->currently);
    }
}
```

### Configuration

In some cases you might have data-structures that contain volatile values (e.g. dynamic timestamps) that will always
trigger a false-positiv for the dirty-check:

#### Ignore fields

If you want to **ignore** certain fields, you can specify which fields should be ignored during the check. If the
configured fields contain complex data _(object or array)_ the affected field and all of it subsequent data will be
**ignored** _(the field acts like a **wildcard**)_:

```php
$userId = 'user:123';
$userData = [
    'username' => 'some-user' 
    'security' => [
        'password' => '...',
        'passwordSalt' => '...',
        'pgp-key' => '...'
    ]
    'meta' => [
        'source' => 'sso'
        'createdAt' => '2024-07-10 15:41:10'
    ]
];

$config = new \Sweikenb\Library\Dirty\Model\ConfigModel(ignoreFieldPath: [
    'security',         // will ignore the whole "security" subset 
    'meta.createdAt'    // will only ignore the "createdAt" field under "meta"
]);

$service = new \Sweikenb\Library\Dirty\Service\DirtyCheckService();

$result = $service->execute($userId, $userData, $config);

if ($result->isDirty) {
    foreach ($result->diffs as $fieldPath => $diff) {
        echo sprintf("Field '%s' is dirty! '%s' -> '%s'\n", $fieldPath, $diff->previously, $diff->currently);
    }
}
```

#### Check only certain fields

You can also explicitly allow fields that should be checked, any other fields will be ignored. If the
configured fields contain complex data _(object or array)_ the affected field and all of it subsequent data will be
**checked** _(the field acts like a **wildcard**)_:

```php
$userId = 'user:123';
$userData = [
    'username' => 'some-user' 
    'security' => [
        'password' => '...',
        'passwordSalt' => '...',
        'pgp-key' => '...',
    ]
    'meta' => [
        'source' => 'sso'
        'createdAt' => '2024-07-10 15:41:10'
    ]
];

$config = new \Sweikenb\Library\Dirty\Model\ConfigModel([
    'username',     // check the "username" field
    'meta',         // check the "meta" field with all containing sub-fields
]);

$service = new \Sweikenb\Library\Dirty\Service\DirtyCheckService();

$result = $service->execute($userId, $userData, $config);

if ($result->isDirty) {
    foreach ($result->diffs as $fieldPath => $diff) {
        echo sprintf("Field '%s' is dirty! '%s' -> '%s'\n", $fieldPath, $diff->previously, $diff->currently);
    }
}
```

#### Combine check and ignore fields

Please note that the "ignore"-configuration will be applied after the "allow"-configuration, that means you can combine
them to enable certain structures and then explicitly remove a single field or subset from it:

```php
$userId = 'user:123';
$userData = [
    'username' => 'some-user' 
    'security' => [
        'password' => '...',
        'passwordSalt' => '...',
        'pgp-key' => '...',
    ]
    'meta' => [
        'source' => 'sso'
        'createdAt' => '2024-07-10 15:41:10'
    ]
];

$config = new \Sweikenb\Library\Dirty\Model\ConfigModel(
    [
        'username',        // check the "username" field
        'meta',            // check the "meta" field with all containing sub-fields
    ],
    [
        'meta.createdAt',  // ignore the "createdAt" sub-field even tough "meta" was explicitly configured to be checked
    ]
);

$service = new \Sweikenb\Library\Dirty\Service\DirtyCheckService();

$result = $service->execute($userId, $userData, $config);

if ($result->isDirty) {
    foreach ($result->diffs as $fieldPath => $diff) {
        echo sprintf("Field '%s' is dirty! '%s' -> '%s'\n", $fieldPath, $diff->previously, $diff->currently);
    }
}
```

## Storage Adapters

Storage adapters and their primary use-cases:

* **Filesystem Adapter** _(default)_
    * local **development** or stage environments
    * single-server setups
    * low amounts of data to check
    * this adapter is **NOT RECOMMENDED** to be used with a network storage mount and highly benefits from a fast
      underlying storage _(e.g. SSD)_
    * files will not be cleaned up automatically, you need to write your own script for that!
* **REDIS Adapter** _(or compatible such as "ValKey")_
    * **Symfony** applications via [DirtyBundle](https://github.com/sweikenb/dirty-bundle)
    * **production** or stage environments
    * multi-server setups
    * any data-set size

You can add custom storage adapters if needed by implementing the `Sweikenb\Library\Dirty\Api\StorageAdapterInterface`.

## Configuration and customization

* _TODO_
