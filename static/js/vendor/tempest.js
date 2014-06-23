(function()
{
	// A collection of front-end tools for working with the Tempest backend.
	// @author Marty Wallace.
	window.Tempest = (function()
	{
		var baseUri = $("base").attr("href") || null;
		
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


			// Holds information about the request that was made to result in the current context.
			Request: (function()
			{
				var request = $("meta[name=request]").attr("content");
	    			    request = request === undefined ? {} : $.parseJSON(B64.decode(request));

				return {

					// Return request data passed to the browser via <code>meta[name=request]</code>, if found.
					// @param group The data group i.e. <code>get</code>, <code>post</code> or <code>named</code> data.
					// @param prop The property name within the group.
					data: function(group, prop)
					{
						if(group === undefined) return request;
						if(!request.hasOwnProperty(group)) return null;

						if(prop === undefined) return request[group];
						if(!request[group].hasOwnProperty(prop)) return null;

						return request[group][prop];
					},


					getRaw: function(){ return request; }
				};

			})(),


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
				if(!base.head.hasOwnProperty("ok")) base.head.ok = base.head.errors.length === 0;
				if(!base.hasOwnProperty("body")) base.body = { };

				return {

					getRaw: function(){ return base; },
					getHead: function(){ return base.head; },
					getErrors: function(){ return base.head.errors; },
					getBody: function(){ return base.body; },
					isOk: function(){ return base.head.ok; }

				};
			}

		}

	})();



	// Manages forms - pushes data through via <code>Tempest.api()</code>. Cleans up data before send,
	// and calls a relevant callback handler when a response is received.
	$("form[action][method] button").on("click", function(event)
	{
		var form = $(this).parents("form");

		if(form.data("default") === "true")
		{
			// Perform normal form submission - ignore Tempest rules below.
			return;
		}

		event.preventDefault();

		if($(this).is("[type=submit]"))
		{
			var data = { };

			// Iterate over input controls and collect data.
			form.find("input, textarea, select").each(function()
			{
				var field = $(this);
				var name = field.attr("name") || field.data("name") || null;

				if(name === null || field.is("[type=button]") || field.is("[type=file]"))
				{
					// Can't work with fields that have no name, are intended for file uploads, or
					// that are buttons.
					return;
				}

				// General input via <code>.val()</code>.
				if(field.is("[type=text]") ||
				   field.is("[type=email]") ||
				   field.is("[type=password]") ||
				   field.is("[type=hidden]") ||
				   field.is("select") ||
				   field.is("textarea"))
				{
					data[name] = field.val();
				}

				// Checkboxes - more suitable/modern behaviour than default.
				if(field.is("[type=checkbox]"))
				{
					data[name] = field.is(":checked") ? 1 : 0;
				}

				// Ratio buttons.
				if(field.is("[type=radio]"))
				{
					data[name] = form.find("input[type=radio][name=" + name + "]:checked").val() || "";
				}

			});


			console.log(data);

			// Post via Tempest.
			Tempest.api(form.attr("method"), form.attr("action"), data, function(response)
			{
				if(form.is("[data-callback]"))
				{
					// TODO: Pass response to callback handler.
					console.log(response);
				}

			});
		}


	});

})();