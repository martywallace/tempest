/// <reference path="../defs/jquery.d.ts" />

module Tempest {

	/**
	 * A response returned from the server after calling <code>Tempest.api()</code>. The data
	 * contained in the response object is parsed JSON.
	 */
	export class Response {

		private _xhr:JQueryXHR;
		private _data:Object;

		constructor(xhr:JQueryXHR, data:Object) {
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
		public getData():Object { return this._data; }

	}

}