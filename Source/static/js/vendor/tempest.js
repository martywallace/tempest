(function()
{
	var baseUri = $("base").attr("href") || null;


	// A collection of front-end tools for working with the Tempest backend.
	// @author Marty Wallace.
	window.Tempest = (function()
	{
		return {

			// Send a request to the application.
			// @param method The request method.
			// @param resource The resource to request data from.
			// @param data The data to send in the request.
			// @param callback The callback method once a response is received.
			api: function(method, resource, data, callback)
			{
				resource = this.path(resource instanceof Array ? resource : resource.split(/\/+/g));
				
				$.ajax({
					url: resource,
					data: data,
					method: method,
					dataType: 'json',

					success: function(response)
					{
						callback && callback(Models.JSONResponse(response));
					},

					error: function(xhr, textStatus, error)
					{
						// Wrap error in JSONResponse model.
						callback && callback(Models.JSONResponse({ head: { errors: [error.message] } }));
					}

				});
			},


			post: function(resource, data, callback){ this.api('post', resource, data, callback); },
			get: function(resource, data, callback){ this.api('get', resource, data, callback); },


			// Generates a valid path from an array of path parts.
			// Will append the baseUri value if it doesn't already exist and the path does not begin with http://, https://, etc.
			// @param value The input value to convert.
			path: function(value)
			{
				var base = value.join("/");
				if(base.indexOf(baseUri) < 0 && base.match(/^\w*:\/\//) === null)
				{
					// Append baseUri if it's not present and if the request is not to
					// an external resource.
					base = baseUri + base;
				}

				return base.replace(/\/+/g, '/').replace(/:\//g, '://');
			},


			getBaseUri: function(){ return baseUri; }

		};

	})();


	// Data wrappers used within Tempest.
	var Models = (function()
	{
		return {
			
			// JSONResponse model wraps the data returned by a <code>Tempest.api()</code> call.
			// @param base The base response data.
			JSONResponse: function(base)
			{
				if(!base.hasOwnProperty("head")) base.head = { };
				if(!base.head.hasOwnProperty("errors")) base.head.errors = [];
				if(!base.head.hasOwnProperty("success")) base.head.success = base.head.errors.length === 0;
				if(!base.hasOwnProperty("body")) base.body = { };

				return {

					getRaw: function(){ return base; },
					getHead: function(){ return base.head; },
					getErrors: function(){ return base.head.errors; },
					getBody: function(){ return base.body; },
					isOk: function(){ return base.head.success; }

				};
			}

		}

	})();

})();