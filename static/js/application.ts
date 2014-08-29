/// <reference path="vendor/tempest.ts" />

/**
 * Client-side application.
 */
module Application
{
	/**
	 * Initialize the application.
	 * @param request The json-encoded request string, supplied by the {{ T_REQUEST_DATA }} token.
	 */
	export function init(request:string):void
	{
		Tempest.Request.setData(request);
	}
}