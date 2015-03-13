/// <reference path="../defs/jquery.d.ts" />

module Tempest
{
    /**
     * Make an API call to a REST endpoint. The REST endpoint must return JSON.
     * @param endpoint The endpoint URL.
     * @param data The data to send.
     * @param method The method to use e.g. Tempest.HTTP.Methods.GET or Tempest.HTTP.Methods.POST.
     * @param callback The callback function to use once the response data is received.
     */
	export function api(endpoint:string, data:Object, method:Tempest.HTTP.Methods, callback:(response:Tempest.Response) => void):void
	{
        $.ajax({

            url: endpoint,
            data: data,
            method: method,
            dataType: 'json',

            success: function(data:any, status:string, xhr:JQueryXHR):void
            {
                callback && callback(new Tempest.Response(xhr, data));
            },

            error: function(xhr:JQueryXHR, status:string, error:string):any
            {
                callback && callback(new Tempest.Response(xhr, { error: error }));
            }

        });
	}
}