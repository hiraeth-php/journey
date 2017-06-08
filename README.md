# Hiraeth/Journey

Hiraeth/Journey is a complete package to provide integration for [Journey](https://github.com/dotink/journey).  It
includes a number of available transformers, as well as a resolver which uses Hiraeth's broker to create and execute
route actions.

## Installation

```bash
composer require hireath/journey
```

Since Hiraeth/Journey will need to operate within the scope of the server request, you will to either need to add its
middleware to your configuration.  The following middleware packages are
supported:

|   Package     |   Middleware                    |   Config Location            |
|---------------|---------------------------------|------------------------------|
| hiraeth/relay | Hiraeth\Journey\RelayMiddleware | relay.jin / middleware.queue |


## Configuration

The primary configuration for Hiraeth/Journey will be copied to your `config/journey.jin` during the installation.  In
addition to this file, it is possible to add a `[journey]` section to any other configuration file for usage.

#### `[journey]`

| Key          | Data Type     | Description
|--------------|---------------|--------------------
| group        | _string_      | A prefix which will be appended to all routes in the section.
| routes       | _object_      | A { key : value } list of routes to controller/actions.
| patterns     | _object_      | A { key : value } list of parameter symbols to their regex patterns.
| transformers | _object_      | A { key : value } list of parameter symbols to their transformer classes.




```json
[journey]

group = ""

routes = {
	"/": "Hiraeth\\Journey\\WelcomeAction"
}

patterns = {
	"!": ".+",
	"#": "\\d+",
	"+": "[1-9][0-9]*",
	"c": "[a-z\\x7f-\\xff][a-z0-9\\x7f-\\xff]*",
	"m": "[a-z\\x7f-\\xff][a-z0-9\\x7f-\\xff]*"
}

transformers = {
	"!": "Hiraeth\\Journey\\StringTransformer"
}
```
