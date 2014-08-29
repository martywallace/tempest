/// <reference path="vendor/tempest.ts" />
/**
* Client-side application.
*/
var Application;
(function (Application) {
    /**
    * Initialize the application.
    * @param request The json-encoded request string, supplied by the {{ T_REQUEST_DATA }} token.
    */
    function init(request) {
        Tempest.Request.setData(request);
    }
    Application.init = init;
})(Application || (Application = {}));
//# sourceMappingURL=application.js.map
