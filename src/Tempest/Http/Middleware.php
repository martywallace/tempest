<?php namespace Tempest\Http;

/**
 * Middleware is used to interact with the Request and Response objects before reaching the Controller. Middleware can
 * be chained in the order defined by the route handler.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
abstract class Middleware extends RequestHandler { }