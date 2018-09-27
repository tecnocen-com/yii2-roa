ROA Definitions
===============

ROA es acronimo para [Resource Oriented Architecture] o Arquitectura Orientada a
Recursos. Esta arquitectura es una especializacion de SOA 
[Service Oriented Architecture] o Arquitectura Orientada a Servicios enfocada en
[Recursos REST] adoptando el formato [HAL].

Esta arquitectura se basa en 3 pilares o mejor dicho 3 piramides que se detallan
inspirados en el [Modelo Richardson] de madurez.

Modelo de Madurez
-----------------

Este es el modelo original propuesto por Leonard Richardson para detallar el
desarrollo de servicios REST.

### Nivel 0: El Pantano de POJX

POJX es un acronimo para Plain Old JSON/XML o JSON/XML Viejo y Plano. Son
servicios que usan el protocolo HTTP para procesar informacion y devuelven
JSON/XML.

Basicamente es cuando se tienen mecanismos HTTP los cuales no cuentan con un
estandar de comunicacion, uso o reutilizacion.

### REST

La madurez de REST se divide en 3 escalones segun Richardson.

#### REST 1: Recursos REST

El primer paso es atomizar el API de forma que cada casos de uso tenga su propio
recurso REST. Esto tambien permite visualizar informacion individual o por
colecciones.

Ejemplo: En lugar de tener una llamada `/inventory` que tenga todas las tiendas
y los productos en cada tienda se tienen recursos `/store`, `/product`
`/store-product`. El primero para la administracion de tiendas, el segundo para
la administracion de productos y el ultimo para relacionar que productos existen
en cada tienda.

#### REST 2: Verbos HTTP

Se utilizan los Verbos HTTP para dar las instrucciones a cada recurso.

Ejemplo en lugar de utilizar una llamada `/createStore` o `/store/create` se usa
`POST /store` de esta forma se tiene consistencia en todas las instrucciones de
todos los recursos y ya que se sabe que POST es para crear, GET para leer,
PATCH para editar y DELETE para borrar agnosticamente del recurso.

#### REST 3: HATEOAS

Como cada caso de uso esta en su propio recurso se necesita que forma de
descubrir la informacion relacional que necesita cada recurso.

### HAL

HAL es un formato que implementa HATEOAS de forma que se puede usar como parte
del Contrato SOA para como se devuelve la informacion al cliente.

#### HAL 1: Enlaces Relacionales

Luego de haber cumplido REST 1 se declaran en cada recurso los enlaces
de los recursos relacionados usando la propiedad `_links`.

Ejemplo en lugar de devolver.

```JSON
{
  "id": "3",
  "name": "Tienda Matriz",
  "encargado_id": "7",
  "encargado_nombre": "Angel",
  "encargado_link": "/user/7"
}
```

se devuelve

```JSON
{
  "id": "3",
  "name": "Tienda Matriz",
  "engargado_id": "7",
  "_links": {
    "self": {
      "href": "/store/3"
    },
    "encargado": {
      "description": "Angel", 
      "href": "/user/7"
    }
  }
}
```

#### HAL 2: Curies

Los curies son enlaces que siguen un template el cual solo cambia en un unico
parametro llamado `{rel}` estos sirven para poder agrupar enlaces relacionales
dependiendo de su uso y aplicacion. El uso mas comun es para poder diferenciar
que recursos se pueden incrustar como se define en el siguiente paso.

#### HAL 2: Recursos Incrustados

En una misma llamada HTTP se puede incrustar informacion de otros recursos
relacionados. Usando la propiedad `_embedded`

Ejemplo la llamada `GET /store/1/product?expand=product,store.manager`

```JSON
[
  {
    "product_id": "1",
    "inventory": "1028",
    "_links": {
        
    }
  },
  {
  }
]
```

SOA
---

A diferencia de REST existen varios modelos de madurez para SOA y no hay un
consenso sobre cual seguir de forma general.

Los modelos de SOA se enfocan en generar una arquitectura que permita
comunicacion entre servicios que no siempre comparten el mismo protocolo,
encriptacion, interfaz de respuesta o punto de acceso. En ROA se asume que todos
los servicios usan HTTP 1.1 como protocolo de comunicacion, HTTPS como cifrado,
HAL como interfaz de respuesta y el mismo punto de acceso para el api.

Por este motivo aqui se detalla un modelo de madurez para SOA el cual esta
simplificado para luego poder seguir definiendo ROA.

#### SOA 1: Contratos

El contrato es un documento que especifica como determinados servicios reciben y
devuelven informacion y metadatos.

Cada contrato debe especificar que protocolos e interfaces se usan para consumir
cada servicio.

Las respuestas devueltas por el servicio deben de ser consistentes en la
estructura de informacion devuelta para los mismos estados de respuesta. Es
decir si un servicio se completa con exito debe de devolver siempre la misma
estructura de informacion. Cada caso de error debe estar documentado y se debe
especificar la estructura de informacion que devuelve cada servicio.

#### SOA 2: Versionamiento

Consiste en organizar los servicios en versiones las cuales deben de soportar
todos los casos de uso del api en si mismos sin depender de versiones previas
o futuras pero si pueden referenciar servicios en otras APIS.

Cada version sigue un ciclo de vida que afecta a todos sus servicios.

- Desarrollo: Los contratos no estan publicados y por lo tanto se pueden editar
- Estable: Una vez que se publican los contratos estos no pueden ser editados
  para que se se puedan consumir de la misma manera durante el resto del ciclo
  de vida.
- Depreciado: Se anuncia que todos los servicios y la version misma seran
  reemplazados por una version mas reciente o abandonados por completo. Se debe
  de definir una fecha a partir de la cual los servicios no estaran disponibles.
- Obsoleto: LOs servicios ya no estan ni estaran disponibles. Se puede ofrecer
  un nuevo punto de acceso para una version mas reciente.

Es necesario que si un servicio soporta algun protocolo o interfaz para su
consumo, todos los servicios deben de soportar ese protocolo o especificar una
alternativa que lo soporte dentro de la misma version.

#### SOA 3: Enrutamiento Biunivoco

Cada servicio debe tener una ruta propia y cada ruta debe apuntar a un solo
servicio de forma biunivoca. Esto aplica para colecciones y registros singulares
por igual.

En particular cada registro singular debe de poder ser accesible por una y una
sola ruta. En ROA esto se relaciona con REST 1 ya que cada servicio debe de
cubrir un unico caso de uso y con HAL 1 ya que los enlaces relacionales ayudan a
econtrar las rutas biunicovas de cada registro singular en especial usando el
enlace `self`.

> Nota: Existen muchas mas condiciones para cumplir SOA que no se abordan en
  este texto debido a que se suplen con REST y HAL. Si quiere leer mas vea
  [SOA vs ROA].

#### ROA 1: SOA / (REST + HAL)

Se refiere a implmentar la arquitectura de SOA pero enfocandola solo en recursos
REST que devuelven informacion en formato HAL. De esta forma se simplifica

De esta forma se simplifican muchas dificultades de SOA al acotar el alcance de
uso y a la vez se da a conocer como se consumen, comunican y reutlizan los
recursos.

#### ROA 2: Codigo de Estado HTTP

Para evitar definir distintos contratos de respuesta en cada recurso para
llamadas exitosas o casos de error se utilizan los [Codigos de Estado HTTP]

Esto se relaciona con SOA 1 principalmente puesto que el mismo codigo de estado
debe devolver la misma estructura de informacion en cada servicio. Por ejemplo
un codigo de estado 404 debe de tener la misma estructura independientemente de
cual recurso se mando invocar.

### ROA 3: Recursos Anidados

Anidar recursos sirve para poder organizarlos mas facilmente y entender el flujo
de uso y creacion. Se utiliza el enrutamiento definido en SOA 3 para evitar
conflictos de duplicidad o redundancia.

Se utilizan los enlaces relacionales definidos en HAL 1 para especificar las
rutas de los recursos que estan anidando y que se pueden anidar.

Ejemplo al solicitar `GET api/v1/tienda/1/venta/3`

```json
{
  "id": 1,
  "amount",
  "_links": {
    "self": {"href": "api/v1/tienda/1/venta/3"},
    "parent_tienda": {"href": "api/v1/tienda/1"},
    "productos": {"href": "api/v1/tienda/1/venta/3/productos"},
    "descuentos": {"href": "api/v1/tienda/1/venta/3/descuentos"},
    "curies": [
      {
        "name": "embeddable",
        "href": "api/v1/tienda/1/venta/3?expand={rel}",
        "templated": true
      }
    ],
    "expand:tienda": "tienda",
    "expand:productos": "productos",
    "expand:descuentos": "descuentos"
  }
}
```

En el ejemplo anterior se usaron CURIES para definir que recursos se pueden
incrustar en una sola peticion si no se desea obtener esta informacion por
separado. De esa forma la peticion
`GET  api/v1/tienda/1/venta/3?expand=tienda,productos,descuentos` devuelve

```json
{
  "id": 1,
  "amount",
  "_links": {
    "self": {"href": "api/v1/tienda/1/venta/3"},
    "parent_tienda": {"href": "api/v1/tienda/1"},
    "productos": {"href": "api/v1/tienda/1/venta/3/productos"},
    "descuentos": {"href": "api/v1/tienda/1/venta/3/descuentos"},
    "curies": [
      {
        "name": "embeddable",
        "href": "api/v1/tienda/1/venta/3?expand={rel}",
        "templated": true
      }
    ],
    "expand:tienda": "tienda",
    "expand:productos": "productos",
    "expand:descuentos": "descuentos"
  },
  "_embedded": {
    "tienda": {"registro singular": "de la tienda"},
    "productos": ["collecion de productos asociados a la venta"],
    "descuentostos": ["collecion de descuentos asociados a la venta"]
  }
}

```

[Resource Oriented Architecture]: https://enwp.org/Resource-oriented_architecture
[Service Oriented Architecture]: https://enwp.org/Service-oriented_architecture
[Recurso REST]: https://enwp.org/Representational_state_transfer
[HAL]: http://stateless.co
[Modelo Richardson]: https://martinfowler.com/articles/richardsonMaturityModel.html
[SOA vs ROA]: soa-vs-roa.md
