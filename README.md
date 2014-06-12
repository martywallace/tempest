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

#### Methods

* <code>path_normalize()</code> - Helper for normalizing a path.
* <code>path_split()</code> - Helper for splitting a path into chunks.
* <code>dtrim()</code> - Trims characters from the left, then trims another set of characters from the right.
* <code>array_keys_prepend()</code> - Prepends the given string on every key in an array. Useful for situations like adding a <code>:</code> for prepared statements.
* <code>debug()</code> - Same as <code>print\_r()</code> but also wraps in <code>&lt;pre&gt;&lt;/pre&gt;</code> for readbility in HTML output.


## Templating

Tempest provides simple templating via the <code>\\Tempest\\Templating\\Template</code> class. Templates can be created on the fly via:

<pre>
$tpl = new Template('&lt;p&gt;My template.&lt;/p&gt;');
</pre>

Or loaded from the <code>/static/</code> directory via the static <code>load()</code> method:

<pre>
$tpl = Template::load("templates/my-template.html");
</pre>

Templates use the typical <code>{{ curlybrace }}</code> syntax seen in most popular templating libraries. Data is bound to templates via the <code>bind()</code> method, e.g.

<pre>
$tpl = new Template("&lt;p&gt;Hello {{ name }}.&lt;/p&gt;");
$tpl->bind(["name" => "John"]);

echo $tpl; // &lt;p&gt;Hello John.&lt;/p&gt;
</pre>

Templates also provide a way to define a context in which properties will be made relevant. The name of the context is passed optionally as the 2nd argument of <code>bind()</code>, and utilitied in the template itself via the syntax <code>{{ @context.property }}</code>. For example:

<pre>
$tpl = new Template("{{ @john.name }} greets {{ @david.name }}.");
$tpl->bind(["name" => "John"], 'john');
$tpl->bind(["name" => "David"]. 'david');

echo $tpl; // John greets David.
</pre>

Tokens support nested properties originating from both arrays and objects. Dot notation is used to access these values, e.g.

<pre>
$tpl = new Template("{{ name.first }}");
$tpl->bind(["name" => ["first" => "John"]]);
</pre>

Methods are also supported by appending <code>()</code> parenthesis onto the relevant values. If the method returns a datastructure with nested properties, those properties can be accessed in the same manner as above. Examples of suitable tokens:

<pre>
{{ someMethod() }}
{{ person.getName().first }}
</pre>

Any occurrence of the value <code>~/</code> within a template will be replaced with the application root. This is useful for creating paths to resources within your application. For example, your main template may include the line:

<pre>
&lt;script src="~/static/js/app.js"&gt;
</pre>

Which will output as something like this:

<pre>
&lt;script src="/MySite/static/js/app.js"&gt;
</pre>

Tokens can be prepended with <code>!</code> for escaping HTML in the result, or <code>?</code> to replace <code>null</code> values with nothing rather than the string <code>"null"</code>:

<pre>
{{ !valueThatIsEscaped }}
{{ ?valueThatIsNull }}
</pre>

Finally, tokens can have hooks attached, which are used to alter the final replacement value. Hooks are added via <code>: hookName</code> at the end of a token. You are able to attach unlimited hooks, and the order of those hooks are preserved when obtaining the final value. Hooks should be added to the <code>\\Tempest\\Templating\\Hooks</code> class, where several are already defined. Example usage:

<pre>
{{ value : sha1 : ucase }} // The resulting value will be an uppercase SHA1 hash of the original.
</pre>


## Sublime Text

The <code>Sublime</code> folder contains various snippets useful for faster development using Tempest. Below are the tab-triggers available.

* <code>tempest::class</code> - A blank class definition.
* <code>tempest::response</code> - A Tempest response class.
* <code>tempest::modelroutes</code> - A block of common routes that deal with a model, formatted as JSON.