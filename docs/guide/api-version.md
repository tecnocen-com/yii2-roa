Api Version
===========

The api versions declared in the version container are modules that
they extend the class `tecnocen\roa\modules\ApiVersion`.

Each version defines its list of resources and the life cycle of its publication.

Version Class Demo
------------------------

```php
V1Version extends \tecnocen\roa\modules\ApiVersion
{
    public $releaseDate = '2010-06-15';
    public $deprecationDate = '2012-01-01';
    public $obsoleteDate = '2012-12-31';

    public $resources = [
        // take class `backend\api\v1\resources\UserResource`
        'user',
        // take class `backend\api\v1\resources\CommentResource`
        'comment',
        // take class `backend\api\v1\resources\comment\ReplyResource`
        'comment/<comment_id:[\d]+>/reply',
        // take class `backend\api\v1\resources\user\AvatarResource`
        'user/avatar',
        'forum' => resources\ForumResource::class, // route class
        'forum/<forum_id:\d>/post' => [
            'class' => resources\PostResource,
            'urlRule' => [
                // special configuration see routing guide.
            ],
        ],
    ];

    public $apidoc = 'http://mockapi.com/api/v1';

    public $controllerNamespace = 'backend\api\v1\resources';

    public $controllerSubfix = 'Resource';
}
```

The use of the `urlRule` key in the example of the` post` resource is detailed in the
guide of [Routing](routing.md)

Resources
--------

The resources of each version are declared in the public property `$ resources`
which consists of an arrangement where each element declares a resource.

Each element of `$ resources` may consist of a string to declare the
route and the associated controller or a partner is automatically deducted
`'path' => 'resource'` where `'resource'` is a specification as defined in
the [resource guide](roa-resource.md).

Api Documentation
-----------------

The `$apidoc` property can store a URL for the api documentation.

Lifecycle
-------------

Each version has a life cycle which consists of 4 stages:
`Development`,` Stable`, `Depreciated` and `Obsolete`.

The change of stages is determined by the variables `$releaseDate`,
`$deprecationDate` and `$obsoleteDate`.

The stability of each version can be obtained with the read-only variable
`$stability`.

Description of Stages in the Life Cycle
---------------------------------------------

### Development

Resources and interfaces are not considered published, so they can be
altered, you can also add new resources.

#### Policies

- SHOULD NOT be more than one type of api in development.

### Stable

When a version is published it becomes stable, this means that it no longer
develop new functionalities and active maintenance is given as
feedback from the end user.

All maintenance must be retrocompatible with resources and interfaces
published from the date of release `$releaseDate`.

#### Policies

- SHOULD NOT be more than one type of stable api.
- NO NEW resources should be published.
- PUBLISHED resources should NOT be deleted.
- Functionality of a published resource MUST NOT be removed.
- Interfaces MUST NOT remove attributes from the information structure.
- Failures of execution and security are corrected.

### Deprecated

It is no longer actively supported, execution corrections are ignored and only
Support for security failures for the end customer and the server.

#### Policies

- SHOULD NOT be more than 2 depreciated apis of the same type.
- NO NEW resources or features should be published.
- NO published resources should be deleted.
- Removing resources MUST return an HTTP status code of 410 GONE.
- Functionality of a published resource should NOT be removed.
- Interfaces MUST NOT remove attributes from the information structure.
- Only security flaws are corrected for the end user or the server.
- NO execution errors MUST be corrected.
- SHOULD receive security maintenance for at least 6 months.
- SHOULD NOT receive security maintenance for more than 12 months.

### Obsolete

At the end of the life cycle, the API and resources are no longer
available for consumption.

#### Policies

- All resources MUST return an HTTP status code of 410 GONE

Stability transitions
---------------------------

### Publication

The process of changing the stability of a version is called 'publication'
from development to stable.

Once the resources have been completed to consume the business flows
of the application and these have been verified as functional, can be changed
the stability of a version module.

To publish a version module it is enough to define the attribute `$releaseDate`
with the date from which the module is considered stable.

```php
    'releaseDate' => '2020-06-15',
```

### Depreciation

It is called 'depreciation' to the process of changing the stability of a version of
stable to depreciated.

It is usually associated to publish a new version with depreciate the version
previously supported.

To depreciate a version module it is necessary to define the attributes
`$deprecationDate` and `$endOfLifeDate`.

```php
    'releaseDate' => '2020-06-15',
    'deprecationDate' => '2021-06-01',
    'obsoleteDate' => '2021-12-31',
```

### End of Life Cycle

It is called 'end of life cycle' the process of changing the stability of a
depreciated version not supported.

This process is automatic as soon as the container detects a version with a
Date `$obsoleteDate` less than the current date.

Version information resource
---------------------------------

When accessing the base route of the version you can find the documentation of
their life cycle, collection of routes and apidoc.
