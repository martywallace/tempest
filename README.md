Tempest
=======

Tempest is a tiny PHP framework providing basic tools for routing and templating.

Note: Project is currently in development and will likely contain numerous bugs.


Routing
------

Tempest provides basic, simple to implement routing. Routes are defined within <code>\app\Application.php</code> via the <code>Router</code>. Routes build an association between a <code>Request</code> and a <code>Response</code>. A simple example of this being:

<pre>
class Application extends \tempest\base\Tempest
{
	
	protected function setup()
	{
		$this->getRouter()->map(array(
			"index" => "BasePage",
			"about" => "BasePage::about",
			"contact" => "ContactPage"
		));
	}

}
</pre>

In this case we handle three routes; <code>/index</code>, which is the default route, <code>/about</code> and <code>/contact</code>. Each of these routes is mapped to a response, which is represented as the class name of the <code>Response</code> object that will handle the request. The response object will then return the output for that request from the relevant response method. The default response method is <code>index()</code>, but routes can point to a different method using the <code>::functionName</code> suffix in the route definition. In the above case, visiting <code>/about</code> will output the return value of the method <code>about()</code> within the <code>BasePage</code> response object. The response method will receive one argument, which is the <code>Request</code> object.

Routes can also contain dynamic components whose request values can be referred to via the <code>Request</code> object. Those sections are marked by wrapping in <code>[]</code>. For example:

<pre>
$routes = array(
	"post/[id]" => "Post",
	"post/[id]/edit" => "Post::edit",
	"post/[id]/delete" => "Post::delete"
);
</pre>

In the <code>Post</code> response, we are able to get the value sent in place of <code>[id]</code> via <code>Request->param(NAMED, 'id')</code>.


Templating
------

General templating tools are also provided, allowing you to load and update strings which can be used for output. By default, templates are looked for in the <code>/view/templates</code> directory and can be loaded using <code>\tempest\templating\Template::load()</code>:

<pre>
using \tempest\base\Template;
$template = Template::load("greeting.html");
</pre>

Because templates are just strings which are loaded and manipulated via the <code>Template</code> utility class, you can also use literals to define a template that <code>Template</code> can work with:

<pre>
$template = "Hello {{ name }}";
</pre>

The first thing to note about loaded templates is that instances of <code>~/</code> will be replaced with the path to the root of your application. This is very useful for links to CSS, JavaScript, images, other pages in the application and so on. For example, including a CSS file may look like:

<pre>
&lt;link rel="stylesheet" href="~/view/css/mycss.css"&gt;
</pre>

Templates generally include tokens, which are defined with typical double curly-braces like this:

<pre>
Hello {{ name }}
</pre>

These tokens can be replated with content using the <code>Template::inject()</code> method:

<pre>
$template = "Hello {{ name }}";
$template = Template::inject($template, array("name" => "Marty"));

echo $template; // Hello Marty
</pre>

Objects can be used in place of arrays, and accessible values of those objects will be injected:

<pre>
class Marty
{
	public $name = "Marty";
}

echo Template::inject("Hello {{ name }}", new Marty());
</pre>

Hierarchical values in both arrays and objects can also be accessed using dot notation:

<pre>
echo Template::inject("Hello {{ person.first }}", array(
	"person" => array("first" => "Marty")
));
</pre>

And function values are accessible by appending <code>()</code>:

<pre>
class Marty
{
	public $name = "Marty";
	public function getUpperName(){ return strtoupper($this->name); }
}

echo Template::inject("Hello {{ getUpperName() }}", new Marty());
</pre>

Tokens can be marked as only relevant in a certain context, which is defined by the optional third argument of <code>Template::inject()</code>. The context of the token is defined by adding a leading <code>@</code> followed by the name of the context. This is useful for ensuring the correct object instances only replace tokens related to them:

<pre>
$template = "{{ @person1.name }} says hello to {{ @person2.name }}";

$template = Template::inject($template, array("name" => "Marty"), 'person1');
$template = Template::inject($template, array("name" => "Daniel"), 'person2');

echo $template; // Marty says hello to Daniel
</pre>

Finally, tokens can be run through a 'hook method'. Hook methods are defined within a class extending <code>\tempest\templating\BaseHookHandler</code> (which implements some of its own hooks). The hook method accepts the value of the token and returns a manipulated value. Unlimited hook methods can be applied, and will be called in the order in which they are defined in the token. Hooks are defined using <code>|</code> followed by the name of the method:

<pre>
Hello {{ name | upcase }} // Single hook.
Hello {{ name | upcase | some | other | hooks }} // Multiple hooks.
</pre>

Your own hooks can be defined by extending <code>BaseHookHandler</code> and defining your hooks within that. Your custom hook handling class then needs to be attached to <code>Template</code> via:

<pre>
Template::setHookHandler( new MyCustomHooks() );
</pre>