/// <reference path="jquery.d.ts" />

/**
 * Provides front-end tools that are designed to work well with the Tempest backend.
 * @author Marty Wallace.
 */
module Tempest
{
	var rdata:any[] = null;

	/**
	 * Exposes request data.
	 */
	export module Request
	{
		export function setData(request:string):void
		{
			rdata = $.parseJSON(request);
		}

		export function data(collection:string = null, field:string = null, fallback:any = null):any
		{
			collection = collection === null ? null : collection.toLowerCase();

			if(collection === null) return rdata;
			if(!rdata.hasOwnProperty(collection)) return fallback;

			if(field === null) return rdata[collection];
			if(!rdata[collection].hasOwnProperty(field)) return fallback;

			return rdata[collection][field];
		}
	}


	export module Utils
	{
		export function trim(input:string):string
		{
			return input.replace(/^\s+|\s+$/g, '');
		}

		export function buildQuery(data:Object):string
		{
			var base:string[] = [];
			for(var param in data) base.push(encodeURIComponent(param) + "=" + encodeURIComponent(data[param]));

			return base.join("&");
		}
	}

	export function api(method:string, endpoint:string, data:Object, callback:(response:JSONResponse) => void):void
	{
		$.ajax({

			url: endpoint,
			data: data,
			method: method,
			dataType: 'json',

			success: function(data:any, status:string, xhr:JQueryXHR):void
			{
				callback && callback(new Tempest.JSONResponse(data));
			},

			error: function(xhr:JQueryXHR, textStatus:string, error:string):any
			{
				callback && callback(new Tempest.JSONResponse({ head: { errors: [error] } }));
			}

		});
	}


	export function get(endpoint:string, data:Object, callback:(response:JSONResponse) => void):void
	{
		api('get', endpoint, data, callback);
	}


	export function post(endpoint:string, data:Object, callback:(response:JSONResponse) => void):void
	{
		api('post', endpoint, data, callback);
	}


	export function getRoot():string
	{
		return rdata.hasOwnProperty("base") ? rdata["base"] : null;
	}


	export function path(value:string[] = [], query:Object = null):string
	{
		var base = value.join("/");
		if(base.indexOf(Tempest.getRoot()) < 0 && base.match(/^\w*:\/\//) === null)
		{
			// Append baseUri if it's not present and if the request is not to
			// an external resource.
			base = Tempest.getRoot() + base;
		}

		return base.replace(/\/+/g, '/').replace(/:\//g, '://') + (query !== null ? "?" + Tempest.Utils.buildQuery(query) : '');
	}


	export class JSONResponse
	{
		private base:any;

		constructor(base:any = null)
		{
			if(base === null)
				base = { head: { ok: true, errors: [] }, body: {} };

			if(!base.hasOwnProperty("head")) base.head = {};
			if(!base.head.hasOwnProperty("ok")) base.head.ok = base.head.hasOwnProperty("errors") ? base.head.errors.length === 0 : true;
			if(!base.head.hasOwnProperty("errors")) base.head.errors = [];
			if(!base.hasOwnProperty("body")) base.body = {};

			this.base = base;
		}

		public isOk():boolean{ return this.base.head.errors.length === 0; }
		public getErrors():string[]{ return this.base.head.errors; }
		public getBody():any{ return this.base.body; }
	}
}