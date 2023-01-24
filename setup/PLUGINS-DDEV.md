# Plugins in DDEV

If you are following the default conventions:

Run `bash setup/plugin-dev`.

If not, follow these instructions and adjust them to your needs.

In order to pull in a plugin living outside the current DDEV project set up a mapping:

Assuming plugins are located in the root directory `~/dev/plugins`, e.g. `~/dev/plugins/test`

Create a file `.ddev/docker-compose.mounts.yaml` adding a docker volume to your project pointing to the plugins root
directory.

```yaml
version: "3.6"
services:
  web:
    volumes:
      - "$HOME/dev/plugins:/var/www/plugins"
```

Add to `composer.json`

```json
{
  "minimum-stability": "dev",
  "prefer-stable": true,
  "repositories": [
    {
      "type": "path",
      "url": "/var/www/plugins/*"
    }
  ]
}
```

Run `ddev restart`

Be sure to run composer commands inside the web container, because the mapping is not available in the host environment.

`ddev composer require wsydney76/craft-test`

This will create a symbolic link:

`/var/www/html/vendor/wsydney76/craft-test ->  /var/www/plugins/test/`

[Source](https://workingconcept.com/blog/ddev-craft-plugin-development)