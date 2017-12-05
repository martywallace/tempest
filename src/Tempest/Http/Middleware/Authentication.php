<?php namespace Tempest\Http\Middleware;

use Closure;
use Tempest\App;
use Tempest\Http\Header;
use Tempest\Http\Middleware;
use Tempest\Http\Request;
use Tempest\Http\Response;
use Tempest\Database\Models\User;
use Tempest\Http\Status;

/**
 * Inbuilt authentication middleware dealing with attaching {@link User users} to the request.
 *
 * @author Marty Wallace
 */
class Authentication extends Middleware {

	/**
	 * Attaches a user to the request based on the X-User-Token header, falling back to a user stored in the session.
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param Closure $next
	 */
	public function resolveUser(Request $request, Response $response, Closure $next) {
		// Look at specifically requested user via X-User-Token header.
		if ($request->hasHeader(Header::X_USER_TOKEN)) {
			$user = User::findByToken($request->getHeader(Header::X_USER_TOKEN)->getValue());

			if (!empty($user)) {
				$request->attachUser($user);
				$next();
			} else {
				$response->setStatus(Status::UNAUTHORIZED);
			}
		}

		// Look at the active session for a user.
		if (App::get()->session->has('UserID')) {
			//
		}
	}

}