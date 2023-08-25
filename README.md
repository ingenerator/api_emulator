## Major work in progress - use at your own risk!

This is a pre-release. Public interfaces are not guaranteed to be backwards compatible between builds, and test coverage
is currently limited.

# API Emulator

This project provides a lightweight HTTP server for designed to support black-box and
integration testing of code that needs to communicate with HTTP APIs.

## Getting started

The emulator runs as a docker container. By default, it answers on port 80 - customise this with the `PORT` environment
variable if required.

All http endpoints that accept data (handlers and emulator management endpoints) automatically support an incoming
Content-Type of either application/json or application/x-www-form-urlencoded.

## Handling requests

The emulator includes a simple regex-based routing layer. This dispatches incoming requests to handlers.

### Default handlers

By default, the emulator defines handlers for:

| HTTP method | URL                    | Description                                                                                                                          |
|-------------|------------------------|--------------------------------------------------------------------------------------------------------------------------------------|
| *           | /ping-200(/{anything}) | Answers every request with a 200 status and a text/plain response "OK". Useful if your code doesn't care about the response content. |

### Custom handlers

You can add custom handlers by mounting (or building) them into the docker container and registering them with the
emulator. A handler is just a simple callable registered against a regex that matches the HTTP method & URL of the
incoming
request. The simplest setup is to define this all in one config file, like so:

```php
# my-project-handlers.php
return [
  // If you provide a custom handlers file this will replace the handlers that ship with the emulator. If you want them
  // to be available as well, just merge the emulator's own config file into yours like so:
  ...require '/api_emulator/default_handlers/handlers.php',
  
  // Then define your own handler here. The pattern will be matched against the complete URL, including any querystring.
  '#^POST /hello-world$#' => function (
       \Psr\Http\Message\ServerRequestInterface $request, 
       Ingenerator\ApiEmulator\HandlerRequestContext $context
     ) : \Psr\Http\Message\ResponseInterface {
       // try to keep handlers as simple as possible. Note that the Guzzle\Psr7\Response class here is provided by
       // the emulator itself.
       return new \Guzzle\Psr7\Response(
           200,
           ['Content-Type' => 'text/plain'],
           'Hi '.$request->getParsedBody()['username']
       );
  },  
];
```

Then run the container like so:

```bash
docker run \
  -e HANDLERS_FILE=/my-project/my-project-handlers.php \
  -v "$PWD/api_emulator":/my-project \
  -p 8080:80
  ghcr.io/ingenerator/api_emulator:main  
```

And then you can speak to it like:

```php
> curl -XPOST -d "{"username": "Brian"} -H "Content-Type:application/json" http://127.0.0.1:8080/hello-world
```

The location of the mounted handlers is entirely arbitrary, so long as your HANDLERS_FILE environment variable points
to the correct path.

The "all-in-one-file" approach obviously doesn't scale very well. For all but the simplest projects we recommend
extracting the handlers to individual files, or grouping them for each API, and using normal PHP `require` and `...`
operations to merge them all into your config file at runtime. For example, the way that the script above merges in
the emulator's default handlers.

### Helper code

You may want to extract helpers and shared code to keep your handlers simple. So long as you mount / build them into
the emulator alongside your handlers you can `require` them in like any other PHP code. Note that there is no way to
add paths to the emulator's own autoloader : just require files manually where you need them.

## Inspecting requests

The emulator automatically captures the full details (method, uri, headers, parsed body) for all incoming requests
(except any to the management interface under `/_emulator-meta`).

To retrieve the request details, call `GET /_emulator-meta/requests`. This will return JSON like:

```json
[
  {
    "id": "2023-08-24-10-57-11-680239",
    "handler_pattern": "#^\\w+ /ping-200$#",
    "uri": "http://api-emulator-http:9000/ping-200?customer_id=219204",
    "method": "POST",
    "headers": {
      "Host": [
        "api-emulator-http:9000"
      ],
      "User-Agent": [
        "Guzzle"
      ],
      "Content-Length": [
        "242"
      ]
    },
    "parsed_body": {
      "email": "brian@foo.test",
      "name": "Brian",
      "categories": [
        "Customer",
        "User"
      ]
    }
  }
]
```

Note that - as with the PSR Request objects - each key in the `headers` object is an **array** of header lines. This
will usually only contain a single entry, but that is not guaranteed as HTTP Header lines are not required to be unique.

## Sharing state data between requests

Handlers should be as simple as possible, ideally requiring predictable stub responses without any need for runtime
setup.

However, sometimes you won't be able to avoid making them stateful. For example:

* You might be calling an API with multiple methods that have to be called in sequence, where the second response
  depends on values that came in the first request.
* Your test code might need to populate non-default status or content of an entity as part of test setup.

The emulator provides a simple data repository to facilitate this.

### Saving and loading state within a handler

Use the data repository passed in to your handler as part of the HandlerRequestContext argument:

```php
use Guzzle\Psr7\Response;
use Ingenerator\ApiEmulator\HandlerRequestContext;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
     
return [
   '#^POST /users$#' => function(ServerRequestInterface $request, HandlerRequestContext $context): ResponseInterface {
      $id = uniqid();
      $context->data_repository->save('/users/'.$id, ['name' => $request->getParsedBody()['name'] ]);
      return new \GuzzleHttp\Psr7\Response(200, ['Content-Type'=> 'application/json'], json_encode(['id' => $id]));
   },
   '#^GET /users/.+$#' => function(ServerRequestInterface $request, HandlerRequestContext $context): ResponseInterface {
     $id = basename($request->getUri());
     if ($context->data_repository->hasPath('users/'.$id)) {
       $data = $context->data_repository->load('/users/'.$id);
       return new Response(200, ['Content-Type'=> 'application/json'], json_encode(['name' => $data['name']]));
     } else {
       return new Response(404, );
     }     
   },
]
```

### Setting state from your test code

The emulator also provides an external HTTP interface for managing test data:

* POST to `/_emulator-meta/handler-data/{path}` to store data.
* DELETE to `/_emulator-meta/handler-data/{path}` to remove it.

For example, to seed the data for the handlers shown above, you could run:

```bash
# Set the data using the emulator's built-in endpoint
curl -X POST \
     -d '{"name": "Brian"}' \
     http://127.0.0.1:8080/_emulator-meta/handler-data/users/81237
     
# The custom handler can now read it - this will print `{"name": "Brian"}`
curl http://127.0.0.1:8080/users/81237
```

## Resetting state

You will usually want to reset the emulator's state for each new testcase. Send `DELETE /_emulator-meta/global-state`
to reset both the handler data repository and the list of captured requests.

## Healthcheck

There is a healthcheck endpoint at `GET /_emulator-meta/health` - you can use this e.g. for a k8s healthcheck, or
to have your test suite wait until the emulator is ready before starting tests.
