Yii2 ROA Library
================

0.5.0 July 13, 2019
-------------------------

- [BRK] Methods now use the typecast supported in php 7.1
- [BRK] Added `tecnocen\roa\hal\Contract::getSelfLink()`

0.4.3 December 14, 2018
----------------------

- [Enh] barebones fix of #35 hard coded self link

0.4.2 October 25, 2018
----------------------

- [Enh] Can use Single Use token with 4.1.0 yii2-oauth2-server

0.4.1 October 10, 2018
----------------------

- [Bug] `ProfileResource` supports options action for the OPTIONS verb.
- [Enh] Simplified `ProfileResource` by extending `yii\rest\Controller`
  directly eliminating the need of several methods.
- [Bug] `Resource` and `ProfileResource` has unpredictable errors when
  searching for unexisten actions or matching verbs and actions.
- [Bug] `Resource` and `ProfileResource` now use
  `yii\filters\VerbFilter` to handle when a verb is not supported.
- [ENh] `tecnocen\roa\hal\Contract` and
  `tecnocen\roa\hal\ContractTrait` define and provide a simple
   implementation of a Hal contrat for ROA services.

0.4.0 September 03, 2018
------------------------

- [Brk] `tecnocen\roa\hal\JsonResponseFormatter` deleted in favor of
  `yii\web\JsonResponseFormatter` (Faryshta)
- [Brk] `tecnocen\roa\controllers\OAuth2Resource` and
  `tecnocen\roa\controllers\OAuth2FileResource` deleted in favor of
  `tecnocen\roa\controllers\Resource`(Faryshta)
- [Enh] Action Filter classes are now implemented on `ApiVersion` and
  `ApiContainer`. See [docs/guide/action-filters.md] (Faryshta)
- [Enh] New class `tecnocen\roa\controllers\Resource` which simplifies resource
  Creation by NOT implementing OAuth2 and other behaviors so they can be
  inherited by parent modules. See [docs/guide/action-filters.md] (Faryshta)
- [Enh] ApiContainer::$responseFormatters declares formatteres to configure
  `Yii::$app->response->formatters` on event `beforeAction`. (Faryshta)
- [Enh] `ApiVersion::$apidoc` admits an URL string which links to the apidoc
  documentation. (Faryshta)
- [Bug] When a verb is not supported by a resource 404 Http Status was sent, fixed
  to 405. (Faryshta)

0.3.1 July 21, 2018
-------------------

- [Bug] `EmbeddableTrait::fields()` and `EmbeddableTrait::extraFields()` turned
  into abstracts to prevent the trait to override implementations by
  `yii\db\ActiveRecord` and `yii\base\Model` (Faryshta)

0.3.0 July 20, 2018
--------------------

- [Enh] New Support to curies
  (See https://tools.ietf.org/html/draft-kelly-json-hal-08) (Faryshta)
- [Enh] Implement support for nestable embedded relation on resources as in
  yii2.0.14 (Faryshta)
- Updated CHANGELOG.md (neverabe)

0.2.3 January 5, 2018
----------------------

- [Bug] fix json validated in sequence (Faryshta)

0.2.2 December 3, 2017
-----------------------

- [Bug] Fix seeResponseMatchesJsonType Array - and self seeResponseContainsJson (neverabe)

0.2.1 November 30, 2017
------------------------

- [Bug] :book: typo (Faryshta)

0.2.0 November 21, 2017
-------------------------

- [Brk] Dropped dependency on filsh in favor of tecnocen/yii2-oauth2-server (Faryshta)

0.1.0 September 27, 2017
-------------------------

- [Brk] `composer.json` Upgrade php requirement to 5.6 (Faryshta)
- [Brk] `tecnocen\roa\actions\Create` load GET and POST data before calling
  `checkAccess()` (Faryshta)

0.0.9 September 7, 2017
-------------------------

- [Enh] `tecnocen\roa\test` namespace with classes to enhance codeception Cest
  classes and improve `Codeception\Actor` to easily create automated tests for
  roa resources. (Faryshta)

0.0.8 August 17, 2017
----------------------

- [Enh] `tecnocen\roa\controllers\OAuth2Resource::$filterParams` parameters to
  filter the records using GET parameters. (Faryshta)
- [Enh] `tecnocen\roa\hal\JsonResponseFormatter` formatter to return json using
  'Content-Type:application/hal+json'. (Faryshta)
- [Enh] `tecnocen\roa\hal\Embeddable`, `tecnocen\roa\hal\EmbeddableTrait`
  interface and trait to create arrays using `_embedded` property and nesting.

0.0.7 July 31, 2017
--------------------

- [Bug] `tecnocen\roa\actions\LoadFileTrait` missing use `yii\web\UploadedFile`.
  (jose1824)

0.0.6 July 31, 2017
--------------------

- [Bug] `tecnocen\roa\actions\LoadFileTrait` missing use `yii\web\UploadedFile`.
  (jose1824)

0.0.5 July 18, 2017
---------------------

- [Bug] simplify slug calls (Faryshta)

0.0.4 July 17, 2017
--------------------

- [Bug] load GET parameters directly (Faryshta)

0.0.3 July 14, 2017
-------------------

- [Enh] `tecnocen\roa\actions\LoadFileTrait` trait to unify the procedure of
  loading uploaded files to the roa actions. (Faryshta)
- [Enh] `tecnocen\roa\controllers\OAuth2FileResource` resource specialized on
  records which will load uploaded files. (Faryshta)

0.0.2 June 30, 2017
--------------------

- [Enh] `tecnocen\roa\urlRules\Composite`, `tecnocen\roa\urlRules\Modular`
  `tecnocen\roa\urlRules\UrlRuleCreator` created for easier creation of routing
  rules (Faryshta)
- [Enh] `tecnocen\roa\urlRules\Composite`, `tecnocen\roa\urlRules\Modular`
  strict mode.
- [Enh] `tecnocen\roa\tests` namespace created for tests, still in blank.
- [Enh] `tecnocen\roa\urlRules\Composite` supports UrlNormalization.
- [Enh] `tecnocen\roa\actions\Create`, `tecnocen\roa\actions\Update`
  added the posibility to upload an update files with dynamic columns (jose1824)
- [Enh] `tecnocen\roa\behaviors\Slug::$idAttribute` array support. (Faryshta)

0.0.1 May 12, 2017
-------------------

-[Enh] Automatic testing was done, future releases might include automatic testing if possible. (Faryshta)
