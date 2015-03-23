module Tempest {

	export module HTTP {

		export enum Methods {
			GET, POST, PUT, DELETE
		}

		/**
		 * Make an API call to a REST endpoint. The REST endpoint must return JSON.
		 * @param endpoint The endpoint URL.
		 * @param data The data to send.
		 * @param method The method to use e.g. Tempest.HTTP.Methods.GET or Tempest.HTTP.Methods.POST.
		 * @param callback The callback function to use once the response data is received.
		 */
		function request(endpoint:string, data:Object, method:Tempest.HTTP.Methods, callback:(response:Tempest.Response) => void):void {
			var methods:string[] = ['get', 'post', 'put', 'delete'];

			$.ajax({
				url: endpoint,
				data: data,
				method: methods[method],
				dataType: 'json',

				success: function(data:any, status:string, xhr:JQueryXHR):void {
					callback && callback(new Tempest.Response(xhr, data));
				},

				error: function(xhr:JQueryXHR, status:string, error:string):any {
					callback && callback(new Tempest.Response(xhr, { error: error }));
				}
			});
		}

		export function get(endpoint:string, data:Object, callback:(response:Tempest.Response) => void):void {
			request(endpoint, data, Tempest.HTTP.Methods.GET, callback);
		}

		export function post(endpoint:string, data:Object, callback:(response:Tempest.Response) => void):void {
			request(endpoint, data, Tempest.HTTP.Methods.POST, callback);
		}
	}
}