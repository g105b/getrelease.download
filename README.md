Download a release from Github using a single fuzzy-matched URL.
================================================================

Examples
--------

+ sloria's doitlive, zip archive, the latest version: https://getrelease.download/sloria/doitlive/*/zip
+ PHPStan, phar archive, latest release: https://getrelease.download/phpstan/phpstan/latest/phar
+ Forty Seven Effect's Arduino MIDI library, zip archive, at least v5.0: https://getrelease.download/FortySevenEffects/arduino_midi_library/>=5.0/zip
+ Pixi.js, minified JavaScript, ^5.3: https://getrelease.download/pixijs/pixi.js/^5.3/pixi.min.js

Usage
-----

The URL takes the following form:

+ `https://getrelease.download`
+ `/org` Github organisation
+ `/repo` Github repository
+ `/1.2.3` Version constraint (semver, or "latest")
+ `/xyz` Asset filename or extension
+ `?file=whatever` Specify the filename (for if there are multiple files with the same extension in the same release)
  
Extras
------

End the URL with the organisation/repository (without version or asset filename) to see a list of available versions.

Example: https://getrelease.download/phpstan/phpstan

End the URL with the version constraint (without asset filename) to see a list of available assets.

Example: https://getrelease.download/phpstan/phpstan/0.12.64

*** 

If you find this tool useful, consider [sponsoring the developer](https://github.com/sponsors/g105b)
