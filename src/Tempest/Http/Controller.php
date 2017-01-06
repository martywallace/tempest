<?php namespace Tempest\Http;

/**
 * A controller defines reactions to routes being triggered. In an ideal scenario, the controller will pass request
 * values off to services and generate a response based on the feedback from those services.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
abstract class Controller implements RequestHandler { }