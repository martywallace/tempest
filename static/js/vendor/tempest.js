/// <reference path="jquery.d.ts" />
/// <reference path="jquery.base64.d.ts" />
/**
* Provides front-end tools that are designed to work well with the Tempest backend.
* @author Marty Wallace.
*/
var Tempest;
(function (Tempest) {
    var rdata = null;

    /**
    * Exposes request data.
    */
    (function (Request) {
        function setData(request) {
            rdata = $.parseJSON(request);
        }
        Request.setData = setData;

        function data(collection, field, fallback) {
            if (typeof collection === "undefined") { collection = null; }
            if (typeof field === "undefined") { field = null; }
            if (typeof fallback === "undefined") { fallback = null; }
            collection = collection === null ? null : collection.toLowerCase();

            if (collection === null)
                return rdata;
            if (!rdata.hasOwnProperty(collection))
                return fallback;

            if (field === null)
                return rdata[collection];
            if (!rdata[collection].hasOwnProperty(field))
                return fallback;

            return rdata[collection][field];
        }
        Request.data = data;
    })(Tempest.Request || (Tempest.Request = {}));
    var Request = Tempest.Request;

    function api(method, endpoint, data, callback) {
        $.ajax({
            url: endpoint,
            data: data,
            method: method,
            dataType: 'json',
            success: function (data, status, xhr) {
                callback && callback(new Tempest.JSONResponse(data));
            },
            error: function (xhr, textStatus, error) {
                callback && callback(new Tempest.JSONResponse({ head: { errors: [error] } }));
            }
        });
    }
    Tempest.api = api;

    function get(endpoint, data, callback) {
        api('get', endpoint, data, callback);
    }
    Tempest.get = get;

    function post(endpoint, data, callback) {
        api('post', endpoint, data, callback);
    }
    Tempest.post = post;

    function getRoot() {
        return rdata.hasOwnProperty("base") ? rdata["base"] : null;
    }
    Tempest.getRoot = getRoot;

    function path(value) {
        if (typeof value === "undefined") { value = []; }
        var base = value.join("/");
        if (base.indexOf(Tempest.getRoot()) < 0 && base.match(/^\w*:\/\//) === null) {
            // Append baseUri if it's not present and if the request is not to
            // an external resource.
            base = Tempest.getRoot() + base;
        }

        return base.replace(/\/+/g, '/').replace(/:\//g, '://');
    }
    Tempest.path = path;

    var JSONResponse = (function () {
        function JSONResponse(base) {
            if (typeof base === "undefined") { base = null; }
            if (base === null)
                base = { head: { ok: true, errors: [] }, body: {} };

            if (!base.hasOwnProperty("head"))
                base.head = {};
            if (!base.head.hasOwnProperty("ok"))
                base.head.ok = base.head.hasOwnProperty("errors") ? base.head.errors.length === 0 : true;
            if (!base.head.hasOwnProperty("errors"))
                base.head.errors = [];
            if (!base.hasOwnProperty("body"))
                base.body = {};

            this.base = base;
        }
        JSONResponse.prototype.isOk = function () {
            return this.base.errors.length === 0;
        };
        JSONResponse.prototype.getErrors = function () {
            return this.base.errors;
        };
        JSONResponse.prototype.getBody = function () {
            return this.base.body;
        };
        return JSONResponse;
    })();
    Tempest.JSONResponse = JSONResponse;
})(Tempest || (Tempest = {}));
//# sourceMappingURL=tempest.js.map
