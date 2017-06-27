[Journey](https://github.com/dotink/journey) is a wrapper and set of extensions for the well known [FastRoute](https://github.com/nikic/fastroute).  It provides thin wrapper which extends FastRoute's native abilities by adding an entry point for dependency resolution, parameter matching shorthands, transformers, and link generation.

## Installation

```bash
composer require hireath/journey
```

The `journey.jin` configuration will be automatically copied to your `config` directory via [opus](https://github.com/imarc/opus).

## Delegates

No delegates are included in this package.

## Providers

No providers are included in this package.

## Configuration

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

| Key          | Data Type     | Description
|--------------|---------------|--------------------
| group        | _string_      | A prefix which will be appended to all routes in the section.
| routes       | _object_      | A { key : value } list of routes to controller/actions.
| patterns     | _object_      | A { key : value } list of parameter symbols to their regex patterns.
| transformers | _object_      | A { key : value } list of parameter symbols to their transformer classes.


The `[journey]` section is globally recognized, so it can be added to any configuration file in the system to add additional routes, patterns, or transformers. Each `[journey]` section is understood as it's own distinct group so all routes within that section will be prefixed by the `group` setting, however, at a global level all patterns, transformers, and routes are added to the same instance, so conflicts are possible.

## usage

The `hiraeth/jouney` package provides middleware as its primary point of integration:

| Middleware                          | Works With
|-------------------------------------|-----------------
| Hiraeth\Journey\RelayMiddleware     | hiraeth/relay
