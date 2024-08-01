# Mock API Bundle
The Mock API Bundle is a Symfony bundle designed to help you mock API requests for testing purposes. It allows you to define mock responses for specific requests using YAML configuration files, making it easier to write integration tests without relying on external services.
This is especially useful when you need to test third-party API integrations or when you want to simulate different scenarios in your tests.

**This will only work in tests, the normal HTTP client behavior will not be affected.**

## Features
- Mock HTTP requests based on URL and request body.
- Define mock responses in YAML files.
- Seamless integration with Symfony's HTTP client.
- Service decoration to ensure usage only in test environments.

## Installation
1. Add the bundle to your Symfony project using Composer:

```bash
composer require bneumann/mock-api-bundle --dev
```

2. Register the bundle in your `config/bundles.php` file:

```php
return [
    // ...
    Bneumann\MockApiBundle\BneumannMockApiBundle::class => ['test' => true],
];
```

3. **(Optional)** Configure the bundle by creating a `bneumann_mock_api.yaml` file in your `config/packages/test` directory:
```yaml
bneumann_mock_api:
    mocks_path: tests/mocks
```

## Usage
To start using the Mock API Bundle, you need to create one or multiple mock configuration file in the directory specified by the `mocks_path` configuration option (default is `tests/mocks`). This file should contain a list of mock responses, each with an URL, method and optional request body for POST and the response data with status code and a body you want to return.

Here's an example of a mock configuration file:

```yaml
mocks:
    - url: https://example.com/api/users
      method: GET
      response:
          status: 200
          body:
              [
                  {
                      "id": 1,
                      "name": "John Doe"
                  },
                  {
                      "id": 2,
                      "name": "Jane Doe"
                  }
              ]
    - url: https://example.com/api/users
      method: POST
      request:
          body:
              name: Alice
      response:
          status: 201
          body:
              {
                  "id": 3,
                  "name": "Alice"
              }
```

You can declare the body or the response and request as JSON or YAML. The example above uses JSON for the response and YAML for the request just for demonstration purposes.

## Contributing
Contributions are welcome! Please feel free to submit a Pull Request or open an Issue to help improve this bundle.

## License
This bundle is open-sourced software licensed under the [MIT license](LICENSE.md).
