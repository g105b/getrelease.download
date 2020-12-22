Download a release from Github using a single fuzzy-matched URL.
================================================================

Examples
--------

+ sloria's doitlive, zip archive, the latest version: https://getrelease.download/sloria/doitlive/*/zip
+ PHPStan, phar archive, >=v0.12: https://getrelease.download/phpstan/phpstan/>=0.12/phar
+ Forty Seven Effect's Arduino MIDI library, zip archive, v5.*: https://getrelease.download/FortySevenEffects/arduino_midi_library/5.*/zip
+ Pixi.js, minified JavaScript, ^5.3: https://getrelease.download/pixijs/pixi.js/^5.3/pixi.min.js

Usage
-----

The URL takes the following form:

+ https://getrelease.download/
+ Github organisation
+ Github repository
+ Version constraint
+ Asset filename or extension
  
Extras
------

End the URL with the organisation/repository (without version or asset filename) to see a list of available versions.

Example: https://getrelease.download/phpstan/phpstan

End the URL with the version constraint (without asset filename) to see a list of available assets.

Example: https://getrelease.download/phpstan/phpstan/0.*

*** 

If you find this tool useful, consider [sponsoring the developer](https://github.com/sponsors/g105b)