/// <reference path="../defs/jquery.d.ts" />

module Tempest {

	/**
	 * A response returned from the server after calling <code>Tempest.api()</code>. The data
	 * contained in the response object is parsed JSON.
	 */
	export class Response {

		private _xhr:JQueryXHR;
		private _data:any;

		constructor(xhr:JQueryXHR, data:any) {
			this._xhr = xhr;
			this._data = data;
		}

		/**
		 * The status code of the response.
		 */
		public getStatus():number { return this._xhr.status; }

		/**
		 * The response data.
		 */
		public getData():any { return this._data; }

	}

}