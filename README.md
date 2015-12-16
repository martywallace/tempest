# Tempest v2.

Yet another minimal PHP framework by [Marty Wallace](http://martywallace.com). Tempest provides a foundation on which you can define services and make use of basic routing.

A very rough overview of the architecture:

![0](http://i.imgur.com/PSW0og3.png)

The core application is accessible via `app()`. Services are accessible by name via `app()`, e.g.

	$html = app()->twig->render('template.html');

## Configuration.

Configuration can be provided via `/app/config.php`. There are a handful of inbuilt configuration options:

<table>
	<thead>
		<tr>
			<th>Option</th>
			<th>Default</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><code>enabled</code></td>
			<td><code>true</code></td>
			<td>Whether or not the site is enabled. If not, the site does not activate any services or trigger any routes. A 503 Service Unavailable response is sent while the site is not enabled.</td>
		</tr>
		<tr>
			<td><code>dev</code></td>
			<td><code>false</code></td>
			<td>Whether or not the application is in development mode. If it is, exceptions and errors are shown to the developer when they are encountered.</td>
		</tr>
		<tr>
			<td><code>url</code></td>
			<td>Educated guess using server name and port variables.</td>
			<td>The application URL e.g. <code>http://yoursite.com</code>.</td>
		</tr>
		<tr>
			<td><code>templates</code></td>
			<td><code>/app/templates</code></td>
			<td>A path or array of paths where application level Twig templates can be loaded from.</td>
		</tr>
		<tr>
			<td><code>routes</code></td>
			<td><code>array()</code></td>
			<td>
				An array of routes mapped to valid handlers. A path relative to the application root pointing to a PHP file that returns an array is also a valid value e.g. <code>/app/routes.php</code>. Valid handler formats include:
				<ul>
					<li><code>["method", "ControllerClassName::methodName", ["MiddlewareClassName::methodName"]]</code></li>
					<li><code>["method", "ControllerClassName::methodName", "MiddlewareClassName::methodName"]</code></li>
					<li><code>["method", "ControllerClassName::methodName"]</code></li>
					<li><code>["ControllerClassName::methodName"]</code></li>
					<li><code>"ControllerClassName::methodName"</code></li>
				</ul>
				Where <code>method</code> is the HTTP method e.g. <code>GET</code> or <code>POST</code>. Middleware is executed in the order they are defined.
			</td>
		</tr>
		<tr>
			<td><code>timezone</code></td>
			<td>Default timezone provied by your PHP installation.</td>
			<td>The application timezone.</td>
		</tr>
		<tr>
			<td><code>robots</code></td>
			<td>-</td>
			<td>If defined, determined the value of the <code>X-Robots-Tag</code> header. Useful for setting <code>noindex</code> and <code>nofollow</code> in staging environments.</td>
		</tr>
		<tr>
			<td><code>db</code></td>
			<td>-</td>
			<td>If defined, provides the connection details used by the internal database service. The value expected in an array with the following keys: <code>host</code>, <code>name</code>, <code>user</code> and <code>pass</code>.</td>
		</tr>
	</tbody>
</table>