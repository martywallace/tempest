# Tempest v2.

Yet another minimal PHP framework by [Marty Wallace](http://martywallace.com). Tempest provides a foundation on which you can define services and make use of basic routing.

A very rough overview of the architecture:

![0](http://i.imgur.com/hGOIaWk.png)

## Installation.

	$ composer require martywallace/tempest

## Usage.

There is a [template available](https://github.com/MartyWallace/tempest-template) as a demonstration of how to get started with Tempest.

## Configuration.

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
			<td><code>dbconfig</code></td>
			<td><code>bool</code></td>
			<td><code>false</code></td>
			<td>Whether or not database level configuration is available. If this is enabled and a valid database connection is set up, the configuration table will be created automatically with the name <code>__config</code>.</td>
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
			<td><code>string</code></td>
			<td>-</td>
			<td>If defined, provides the connection details used by the internal database service. The value expected in a string in the following format: <code>mysql://user:password@host/database</code>.</td>
		</tr>
		<tr>
			<th colspan="4">Paths</th>
		</tr>
		<tr>
			<td><code>templates</code></td>
			<td><code>string|string[]</code></td>
			<td>---</td>
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