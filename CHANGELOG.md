Yii2 ROA Library
================

0.0.8
-----

- [Enh] `tecnocen\roa\controllers\OAuth2Resource::$filterParams` parameters to
  filter the records using GET parameters. (Faryshta)
- [Enh] `tecnocen\roa\hal\JsonResponseFormatter` formatter to return json using
  'Content-Type:application/hal+json'. (Faryshta)
- [Enh] `tecnocen\roa\hal\Embeddable`, `tecnocen\roa\hal\EmbeddableTrait`
  interface and trait to create arrays using `_embedded` property and nesting.

0.0.7
-----

- [Bug] `tecnocen\roa\actions\LoadFileTrait` missing use `yii\web\UploadedFile`.
  (jose1824)

0.0.3
-----

- [Enh] `tecnocen\roa\actions\LoadFileTrait` trait to unify the procedure of
  loading uploaded files to the roa actions. (Faryshta)
- [Enh] `tecnocen\roa\controllers\OAuth2FileResource` resource specialized on
  records which will load uploaded files. (Faryshta)

0.0.2
-----

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
