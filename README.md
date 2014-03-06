Tempest
=======

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

In this case we handle three routes; <code>index</code>, which is the default route, <code>about</code> and <code>contact</code>. Each of these routes is mapped to a response, which is represented as the class name of the <code>Response</code> object that will handle the request. The response object will then return the output for that request from the relevant response method. The default response method is <code>index()</code>, but routes can point to a different method using the <code>::functionName</code> suffix in the route definition. In the above case, visiting <code>/about</code> will output the return value of the method <code>about()</code> within the <code>BasePage</code> response object.

Routes can also contain dynamic components whose request values can be referred to via the <code>Request</code> object. Those sections are marked by wrapping in <code>[]</code>. For example:

<pre>
$routes = array(
	"post/[id]" => "Post",
	"post/[id]/edit" => "Post::edit",
	"post/[id]/delete" => "Post::delete"
);
</pre>

In the <code>Post</code> response, we are able to get the value sent in place of <code>[id]</code> via <code>Request->param()</code>.