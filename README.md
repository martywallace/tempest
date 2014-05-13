# Tempest

Currently being redeveloped - [older, stable version found here](https://github.com/MartyWallace/Tempest/tree/7c42c8cbed3b049f107b2f266419e7ddc1a9c8c1).


## Globals

Tempest defines a collection of global constants and methods.

#### Constants

* <code>RGX_PATH_DELIMITER</code> - Regex pattern for matching one or more forward or black slashes.
* <code>APP_ROOT</code> - The application root on the server e.g. <code>C:\\...\\MySite\\</code>.
* <code>PUBLIC_ROOT</code> - The public application root e.g. <code>/MySite/</code>.
* <code>DIR_SERVER</code> - The <code>server</code> directory within the application e.g. <code>C:\\...\MySite\\server\\</code>.
* <code>DIR_STATIC</code> - The <code>static</code> directory within the application e.g. <code>/MySite/static/</code>.
* <code>REQUEST_CLEAN</code> - The request URI with querystring and hash values trimmed off.
* <code>REQUEST_URI</code> - The full request URI e.g. <code>/MySite/some/route</code>.
* <code>APP_REQUEST_URI</code> - The request URI relative to the application root e.g. <code>/some/route</code>.

#### Methods

* <code>path_normalize</code>
* <code>path_split</code>
* <code>str_comma_join</code>
* <code>str_needle_remove</code>