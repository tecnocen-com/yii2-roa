Ejecutar Pruebas
================

Las aplicaciones y librerias de Yii2 ROA cuentan con soporte para pruebas
automaticas y atajos para su fácil creación y ejecución.

Pruebas en Aplicaciones
-----------------------

Yii2 App ROA cuenta con soporte para pruebas las cuales se desplegan cuando
se inicializa un nuevo proyecto usando los comandos de composer.

Existen dos formas para ejecutar las pruebas, usando los comandos de composer
que proveen un atajo para las pruebas mas comunes y usando el binario de
Codeception.

- `composer run-tests` ejecuta las pruebas
- `composer run-tests-debug` ejecuta las pruebas mostrando mas información.
- `composer run-coverage`  ejecuta las pruebas y genera el archivo de cobertura
  de código.

Se pueden configurar o agregar mas comandos en el archivo `composer.json`.

La otra opción es usar el archivo binario de Codeception directamente para tener
acceso a todas las opciones que este permite.

`./vendor/bin/codecept run`

Pruebas en librerias
--------------------

Las librerias de Yii2 ROA como formgenerator y workflow cuentan con atajos para
desplegar y ejecutar las pruebas.

### Desplegar pruebas

Las librerias vienen preconfiguradas pero es necesario introducir los accesos
para la base de datos en el archivo `tests/_app/config/db.local.php`.

```php
<?php

return [
    'password' => 'root',
];
```

Crear la base  de datos definida en el archivo tests/_app/config/db.php`

`create database yii2_workflow_test`

Y por último ejecutar el comando de despliegue.

`composer deploy-tests`

### Ejecutar pruebas

Al igual que las aplicaciones existen dos formas para ejecutar las pruebas,
usando los comandos de composer que proveen un atajo para las pruebas mas
comunes y usando el binario de Codeception.

- `composer run-tests` ejecuta las pruebas
- `composer run-coverage`  ejecuta las pruebas y genera el archivo de cobertura
  de código.

La otra opción es usar el archivo binario de Codeception directamente para tener
acceso a todas las opciones que este permite.

`./vendor/bin/codecept run`
