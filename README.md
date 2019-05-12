# response

Simple library to abstract response data and provide output schema validation
capabilities based on PHP Filter extension.

The use of [`ValidArray`](https://github.com/vertilia/valid-array) mechanism
guarantees the output will match the predefined response schema.

## Use case

Your controller receives the `HttpResponse` object with predefined filters.
Filters configuration corresponds to the response schema for the particular
route.

To populate response object with values, controller sets them with normal array
access operators. At this moment validation mechanism evaluates the value and
sanitizes it if necessary to correspond to the schema.

Items not present in filters list are discarded. Invalid values are set to
false. Correct values are set as is. Items present in validation schema but not
set take the value `null`.

In the following example request controller `UserController` receives an
`HttpRequest` object with request information and `JsonResponse` object with
preconfigured output headers and filters. It populates and returns the
`JsonResponse` object which is then asked to render itself at the end of the
request.

```php
<?php

function getResponseForOperationId(
    string $operationId,
    HttpRequest $request
): HttpResponseInterface {
    switch ($operationId) {
        case 'getUserEmailById':
            $user = new UserController($request);

            $response = new JsonResponse([
                'id' => \FILTER_VALIDATE_INT,
                'email' => \FILTER_VALIDATE_EMAIL,
            ]);

            return $user->getEmailByIdResponse($response);

        default:
            return new NotFoundResponse();
    }
}

// initialise HttpRouter with $request and $routes
$router = new HttpRouter($request, $routes);

// get operation id from router
$operationId = $router->getOperationId();

// get HttpResponse object and render it
$response = getResponseForOperationId($operationId, $request);
$response->render();
```

Since `JsonResponse` object is generated per operation, it includes exactly the
filters needed for this specific operation. Populating and returning this
response object guarantees that the rendered version will corespond to response
schema.

How to instantiate `HttpRequest` and `HttpRouter` services is left as an
exercise to the reader. If interested, have a look at
[`vertilia/request`](https://github.com/vertilia/request) and
[`vertilia/router`](https://github.com/vertilia/router) packages.
