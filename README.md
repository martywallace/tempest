# Tempest v2.

Yet another minimal PHP framework by [Marty Wallace](http://martywallace.com). Tempest provides a foundation on which you can define services and make use of basic routing.

A very rough overview of the architecture:

![0](http://i.imgur.com/hGOIaWk.png)

The core application is accessible via `app()`. Services are accessible by name via `app()`, e.g.

	$html = app()->twig->render('template.html');

## Configuration.

Configuration can be provided via `/app/config.php`. Configuration can be accessed using:

	app()->config('variable');

The inbuilt configuration options are:

<table>
	<thead>
		<tr>
			<th>Option</th>
			<th>Type</th>
			<th>Default</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th colspan="4">Core</th>
		</tr>
		<tr>
			<td><code>enabled</code></td>
			<td><code>bool</code></td>
			<td><code>true</code></td>
			<td>Whether or not the site is enabled. If not, the site does not activate any services or trigger any routes. A 503 Service Unavailable response is sent while the site is not enabled.</td>
		</tr>
		<tr>
			<td><code>dev</code></td>
			<td><code>bool</code></td>
			<td><code>false</code></td>
			<td>Whether or not the application is in development mode. If it is, exceptions and errors are shown to the developer when they are encountered.</td>
		</tr>
		<tr>
			<td><code>url</code></td>
			<td><code>string</code></td>
			<td>Educated guess using server name and port variables.</td>
			<td>The application URL e.g. <code>http://yoursite.com</code>.</td>
		</tr>
		<tr>
			<td><code>timezone</code></td>
			<td><code>string</code></td>
			<td>Default timezone provided by your PHP installation.</td>
			<td>The application timezone.</td>
		</tr>
		<tr>
			<td><code>key</code></td>
			<td><code>string</code></td>
			<td>-</td>
			<td>The key used to encrypt and decrypt data via the <code>crypt</code> service.</td>
		</tr>
		<tr>
			<th colspan="4">Routing</th>
		</tr>
		<tr>
			<td><code>routes</code></td>
			<td><code>array[]</code></td>
			<td><code>array()</code></td>
			<td>
				An array containing route information. Valid route information can be in any of these formats:
				<ul>
					<li><code>[uri, controller]</code></li>
					<li><code>[uri, method, controller]</code></li>
					<li><code>[uri, method, ...middleware, controller]</code></li>
				</ul>
				Where <code>method</code> is the HTTP method e.g. <code>GET</code> or <code>POST</code>. Middleware is executed in the order they are defined.
			</td>
		</tr>
		<tr>
			<th colspan="4">Connections</th>
		</tr>
		<tr>
			<td><code>db</code></td>
			<td><code>array</code></td>
			<td>-</td>
			<td>If defined, provides the connection details used by the internal database service. The value expected in an array with the following keys: <code>host</code>, <code>name</code>, <code>user</code> and <code>pass</code>.</td>
		</tr>
		<tr>
			<th colspan="4">Paths</th>
		</tr>
		<tr>
			<td><code>templates</code></td>
			<td><code>string|string[]</code></td>
			<td><code>/app/templates</code></td>
			<td>A path or array of paths where application level Twig templates can be loaded from.</td>
		</tr>
		<tr>
			<td><code>controllers</code></td>
			<td><code>string</code></td>
			<td><code>Controllers</code></td>
			<td>The base namespace for all controller classes.</td>
		</tr>
		<tr>
			<td><code>middleware</code></td>
			<td><code>string</code></td>
			<td><code>Middleware</code></td>
			<td>The base namespace for all middleware classes.</td>
		</tr>
	</tbody>
</table>

Additional configuration options can be of course provided by adding your own `key => value` pairs.