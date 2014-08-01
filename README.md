# Tempest

Tempest is a tiny PHP framework.


## Globals

Tempest defines a collection of global constants and methods. Constants are defined within <code>index.php</code> and methods in <code>/server/common/functions.php</code>.

#### Constants

* <code>DIR</code> - Exactly the same value as <code>\_\_DIR\_\_</code> would provide from <code>index.php</code>.
* <code>SEP</code> - Shorthand for <code>DIRECTORY_SEPARATOR</code>.
* <code>GET</code> - Used where the string <code>'get'</code> could otherwise be used.
* <code>POST</code> - Used where the string <code>'post'</code> could otherwise be used.
* <code>NAMED</code> - Used where the string <code>'named'</code> could otherwise be used.
* <code>RGX_PATH_DELIMITER</code> - Regex pattern for matching one or more forward or black slashes.
* <code>RGX_TEMPLATE_TOKEN</code> - Regex pattern for matching tokens within a template.
* <code>APP_ROOT</code> - The application root on the server e.g. <code>C:\\...\\MySite\\</code>.
* <code>PUB_ROOT</code> - The public application root e.g. <code>/MySite/</code>.
* <code>REQUEST_CLEAN</code> - The request URI with querystring and hash values trimmed off.
* <code>REQUEST_URI</code> - The full request URI e.g. <code>/MySite/some/route</code>.
* <code>APP_REQUEST_URI</code> - The request URI relative to the application root e.g. <code>/some/route</code>.
* <code>IS_LOCAL</code> - Returns <code>true</code> if the application is running under <code>localhost</code> or <code>127.0.0.1</code>.

#### Methods

* <code>path_normalize()</code> - Helper for normalizing a path.
* <code>path_split()</code> - Helper for splitting a path into chunks.
* <code>dtrim()</code> - Trims characters from the left, then trims another set of characters from the right.
* <code>array_keys_prepend()</code> - Prepends the given string on every key in an array. Useful for situations like adding a <code>:</code> for prepared statements.
* <code>set_or()</code> Returns the first value if it is set (<code>isset()</code> returns true), else the latter (which defaults to <code>null</code>). Essentially a shorthand for <code>isset(a) ? a : b</code>.
* <code>pre_print_r()</code> Same as <code>print_r</code>, but the output is wrapped in a <code>&lt;pre&gt;&lt;/pre&gt;</code> block.
* <code>fetch_data()</code> Fetch data from a given URL via <code>GET</code> or <code>POST</code>.