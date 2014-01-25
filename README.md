Tempest
=======

A small PHP framework providing common tools for rapid website and application development.


Quick Start
--

To get started, extract the files into the root of your application. <code>/server/app/Application.php</code> is the entrypoint for your code, where you will define routes that your application will handle.

In an example scenario where your application has 2 pages: <code>Home</code> and <code>About</code>, your <code>Application</code> might look something like this:

<pre>
namespace app;

class Application extends \tempest\base\Tempest
{
    protected function setup()
    {
        $routes = array(
            "home" => "app.pages.Home",
            "about" => "app.pages.About"
        );
    
        // The 2nd argument defines the route that will be used when you are visiting the site root.
        $this->getRouter()->map($routes, 'home');
    }
}
</pre>


Routing &amp; Responses
--

The value of each item in the <code>$routes</code> array represents a class extending <code>\tempest\routing\Response</code>. Each <code>Response</code> has a <code>respond()</code> method which will return the output for that request. When a request is made to the server, the keys in <code>$routes</code> are checked for a match. If there is a match between the request and a key, a <code>Response</code> object will be constructed and its <code>respond()</code> method called.

In this example, the responses <code>Home</code> and <code>About</code> would be stored within <code>/server/app/pages/</code> and look something like this:

<pre>
namespace app\pages;

class Home extends \tempest\routing\Response
{
    protected function respond($request)
    {
        return "Hello world!";
    }
}
</pre>
