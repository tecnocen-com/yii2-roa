ROA Definitions
===============

ROA is an acronym for [Resource Oriented Architecture] or Oriented Architecture
Resources. This architecture is a SOA specialization
[Service Oriented Architecture] or Service Oriented Architecture focused on
[REST resources] adopting the format [HAL].

This architecture is based on 3 pillars or rather 3 pyramids that are detailed
inspired by the [Richardson Model] of maturity.

Maturity Model
-----------------

This is the original model proposed by Leonard Richardson to detail the
development of REST services.

### Nivel 0: The Swamp of POX

POJX is an acronym for Plain Old JSON / XML or JSON / XML Old and Flat. Are
services that use the HTTP protocol to process information and return
JSON / XML.

Basically it is when you have HTTP mechanisms which do not have a
standard of communication, use or reuse.

### REST

The maturity of REST is divided into 3 steps according to Richardson.

#### REST 1: Resources REST

The first step is to atomize the API so that each use case has its own
REST resource. This also allows you to view individual information or by
collections

Example: Instead of having an `/ inventory` call that has all the stores
and the products in each store have resources `/ store`,` / product`
`/ store-product`. The first for store management, the second for
the administration of products and the last one to relate what products exist
in each store.

#### REST 2: HTTP Verbs

HTTP Verbs are used to give instructions to each resource.

Example instead of using a call `/ createStore` or` / store / create` is used
`POST / store` in this way is consistent in all the instructions of
all the resources and since it is known that POST is to create, GET to read,
PATCH to edit and DELETE to agnostically delete the resource.

#### REST 3: HATEOAS

As each use case is in its own resource, what form of
Discover the relational information that each resource needs.

### HAL

HAL is a format that implements HATEOAS so that it can be used as part of
of the SOA Contract for how the information is returned to the client.

#### HAL 1: Relational links

After having fulfilled REST 1, the links are declared in each resource
of related resources using the `_links` property.

Example instead of returning.

```JSON
{
  "id": "3",
  "name": "Store 1",
  "employee_id": "7",
  "employee_name": "Angel",
  "employee_link": "/user/7"
}
```

it is returned

```JSON
{
  "id": "3",
  "name": "Store 1",
  "employee_id": "7",
  "_links": {
    "self": {
      "href": "/store/3"
    },
    "employee": {
      "description": "Angel", 
      "href": "/user/7"
    }
  }
}
```

#### HAL 2: Curies

The curies are links that follow a template which only changes in a single
parameter called `{rel}` these are used to group relational links
depending on its use and application. The most common use is to be able to differentiate
which resources can be embedded as defined in the next step.

#### HAL 3: Embedded Resources

You can embed information from other resources in the same HTTP call
related Using the `_embedded` property

Example the call `GET /store/1/product?expand=product,store.manager`

```JSON
[
  {
    "product_id": "1",
    "inventory": "1028",
    "_links": {
        
    }
    "_embedded": {

    }
  },
  {
  }
]
```

SOA
---

Unlike REST, there are several maturity models for SOA and there is no
consensus on what to follow in a general way.

The SOA models focus on generating an architecture that allows
communication between services that do not always share the same protocol,
encryption, response interface or access point. In ROA it is assumed that all
the services use HTTP 1.1 as communication protocol, HTTPS as encryption,
HAL as response interface and the same access point for the api.

For this reason, a maturity model for SOA is detailed here which is
simplified to then be able to continue defining ROA.

#### SOA 1: Contacts

The contract is a document that specifies how certain services receive and
return information and metadata.

Each contract must specify which protocols and interfaces are used to consume
each service.

The responses returned by the service must be consistent in the
structure of information returned for the same response states. Is
to say if a service is completed successfully must always return the same
information structure. Each case of error must be documented and must be
specify the information structure that each service returns.

#### SOA 2: Versioning

It consists of organizing services in versions which must support
all the use cases of the api in themselves without relying on previous versions
or future ones but if they can reference services in other APIS.

Each version follows a life cycle that affects all its services.

- Development: The contracts are not published and therefore can be edited
- Stable: Once the contracts are published they can not be edited
  so that they can be consumed in the same way during the rest of the cycle
  of life.
- Depreciated: It is announced that all services and the version itself will be
  replaced by a newer version or completely abandoned. Must be
  to define a date from which the services will not be available.
- Obsolete: Services are no longer available and will not be available. It can be offered
  a new access point for a more recent version.

It is necessary that if a service supports some protocol or interface for its
consumption, all services must support this protocol or specify a
alternative that supports it within the same version.

#### SOA 3: Bijective Routing

Each service must have its own route and each route must point to a single
bijective service. This applies to collections and unique records
equally.

In particular, each unique record must be accessible by one and one
single route. In ROA this is related to REST 1 since each service must
cover a single use case and with HAL 1 since relational links help
find the biunicov routes of each singular record in particular using the
link `self`.

> Note: There are many more conditions to meet SOA that are not addressed in
  this text because they are supplemented with REST and HAL. If you want to read more see
  [SOA vs. ROA].

#### ROA 1: SOA / (REST + HAL)

It refers to implment the SOA architecture but focusing only on resources
REST that return information in HAL format. This simplifies

In this way, many SOA difficulties are simplified by narrowing the scope of
use and at the same time it is made known how they are consumed, communicated and reused.
resources.

#### ROA 2: HTTP Status Code

To avoid defining different response contracts in each resource for
Successful calls or error cases are used [HTTP Status Codes]

This is related to SOA 1 mainly because the same state code
must return the same information structure in each service. For example
a 404 status code must have the same structure regardless of
which resource is commanded to invoke.

### ROA 3: Nested Resources

Nesting resources serves to organize them more easily and understand the flow
of use and creation. The routing defined in SOA 3 is used to avoid
conflicts of duplicity or redundancy.

The relational links defined in HAL 1 are used to specify the
routes of resources that are nesting and that can be nested.

Example when requesting `GET api/v1/store/1/sale/3`

```json
{
  "id": 1,
  "amount",
  "_links": {
    "self": {"href": "api/v1/store/1/sale/3"},
    "store_parent": {"href": "api/v1/store/1"},
    "products": {"href": "api/v1/store/1/sale/3/products"},
    "discounts": {"href": "api/v1/store/1/sale/3/discounts"},
    "curies": [
      {
        "name": "embeddable",
        "href": "api/v1/store/1/sale/3?expand={rel}",
        "templated": true
      }
    ],
    "expand:store": "store",
    "expand:products": "products",
    "expand:discounts": "discounts"
  }
}
```

In the previous example, CURIES were used to define what resources can be used
embed in a single request if you do not want to obtain this information by
separated. In that way the petition
`GET  api/v1/store/1/sale/3?expand=store,products,discounts` returns

```json
{
  "id": 1,
  "amount",
  "_links": {
    "self": {"href": "api/v1/store/1/sale/3"},
    "parent_store": {"href": "api/v1/store/1"},
    "products": {"href": "api/v1/store/1/sale/3/products"},
    "discounts": {"href": "api/v1/store/1/sale/3/discounts"},
    "curies": [
      {
        "name": "embeddable",
        "href": "api/v1/store/1/sale/3?expand={rel}",
        "templated": true
      }
    ],
    "expand:store": "store",
    "expand:products": "products",
    "expand:discounts": "discounts"
  },
  "_embedded": {
    "store": {"singular record of the store"},
    "products": ["collection of products associated with the sale"],
    "discounts": ["collecion of discounts asociados with the sale"]
  }
}

```

Conclusion
----------

ROA can be interpreted as an SOA which focuses only on REST + HAL
and takes full advantage of the advantages they offer to simplify the
development of services and reduce their delivery time.

It is recommended for APIs that are only planned to be consumed
only using the HTTP 1.1 protocol.

This model of maturity is a guide on how to achieve an implementation of ROA
fully functional.

[Resource Oriented Architecture]: https://enwp.org/Resource-oriented_architecture
[Service Oriented Architecture]: https://enwp.org/Service-oriented_architecture
[Recurso REST]: https://enwp.org/Representational_state_transfer
[HAL]: http://stateless.co
[Modelo Richardson]: https://martinfowler.com/articles/richardsonMaturityModel.html
[SOA vs ROA]: soa-vs-roa.md
