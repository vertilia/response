# response

Simple library to abstract response data for several popular formats and provide output schema validation capabilities
based on `php-filter` extension.

The use of [`ValidArray`](https://github.com/vertilia/valid-array) mechanism guarantees the output will match the
predefined response schema.

## Use case

Your controller is an `HttpResponse` object with predefined filters. Filters configuration corresponds to the response
schema for the particular route and is defined in leaf data of routes file under `response` element.

During pre-rendering phase controller sets corresponding elements as defined in `response`. At this moment validation
mechanism evaluates values and sanitizes them if necessary to correspond to the filtering rules.

Items not present in filters list are discarded. Invalid values are set to `false`. Correct values are set as is. Items
present in validation schema but not set take `null` value. Rules may be modified using `php-filter` flags constants
and `ValidArray` enhancements, including default values for all filters (not only `FILTER_VALIDATE_*` filters).

In the following example request controller `UserGetEmail` is defined in route file as a handler for
`GET /api/users/{id}/email` route. On creation, it receives validation filters and an `HttpRequest` object with request
information. When `render()` method is called on this controller, it renders an HTTP response as JSON text with
corresponding headers, this work is done by `JsonResponse`. What is handled by the controller itself is located in
`preRender()` method. It populates controller elements `id` (from request) and `email` (from `User` object). These
pre-validated elements will correspond the initial response schema and will be output in a JSON format (like
`{"id":42,"email":"user@example.com"}`).

```php
<?php   // etc/api-routes.php

return [
    'GET /api/users/{id}/email' => [
        'controller' => App\Controller\UserGetEmail::class,
        'filters' => [
            'id' => ['filter' => FILTER_VALIDATE_INT, 'flags' => FILTER_REQUIRE_SCALAR],
        ],
        'response' => [
            'id' => FILTER_VALIDATE_INT,
            'email' => FILTER_VALIDATE_EMAIL,
        ],
    ]
];
```

```php
<?php   // web/index.php

use Vertilia\Request\HttpRequest;
use Vertilia\Response\JsonResponse;
use Vertilia\Router\HttpRouter;

// initialize request object
$request = new HttpRequest(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES,
    file_get_contents('php://stdin')
);

// initialise HttpRouter with request and a list of routes
$router = new HttpRouter($request, ['../etc/api-routes.php']);

// get leaf structure from router, inject filters and parameters into $request
// elements to be present in leaf structure:
// - "container" to identify controller name
// - "filters" to validate request parameters filters
// - "response" filters for response structure
$leaf_structure = $router->getControllerFromRequest(App\NotFoundResponse::class);

// get HttpResponse object and render it
$response = new ($leaf_structure['controller'])($leaf_structure['response'] ?? [], $request);
$response->render();
```

```php
<?php   // app/Controller/UserGetEmail.php

namespace App\Controller;

use Vertilia\Request\HttpRequest;
use Vertilia\Response\JsonResponse;

class UserGetEmail extends JsonResponse
{
    protected $request;

    public function __construct(array $filters, HttpRequest $request)
    {
        parent::__construct($filters);
        $this->request = $request;
    }

    public function preRender()
    {
        $this['id'] = $this->request['id'];
        $user = new App\Model\User($this['id']);
        $this['email'] = $user->getEmail();
    }
}
```

Since `JsonResponse` object is generated per operation, it includes exactly the filters needed for this specific
operation. Populating and returning this response object guarantees that the rendered version will correspond to
response schema.

## Extending basic use case

Implementing preliminary check of required request parameters is what we leave to the user land since it is normally
coupled with error messaging. If interested in implementing localised text messages, have a look at
[`vertilia/text`](https://github.com/vertilia/text) package on GitHub.

Correct instantiation and more use cases for `HttpRequest` and `HttpRouter` services may be found in corresponding
packages, have a look at [`vertilia/request`](https://github.com/vertilia/request) and
[`vertilia/router`](https://github.com/vertilia/router) pages.

A more complex controller implementation, handling request validation errors and multiple responses per route as defined
in [OpenAPI specification](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#responses-object)
may be found in [`vertilia/controller`](https://github.com/vertilia/controller) package.