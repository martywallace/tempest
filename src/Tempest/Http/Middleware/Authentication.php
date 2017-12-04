<?php namespace Tempest\Http\Middleware;

use Closure;
use Tempest\Http\Header;
use Tempest\Http\Middleware;
use Tempest\Http\Request;
use Tempest\Http\Response;
use Tempest\Database\Models\User;

/**
 * Inbuilt authentication middleware dealing with attaching {@link User users} to the request.
 *
 * @author Marty Wallace
 */
class Authentication extends Middleware {

	/**
	 * Attaches a user to the request based on
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param Closure $next
	 */
	public function attachUser(Request $request, Response $response, Closure $next) {
		// Look at specifically requested user via X-User-Token header.
		if ($request->hasHeader(Header::X_USER_TOKEN)) {
			$creds = explode(':', base64_decode($request->getHeader(Header::X_USER_TOKEN)));

			if (count($creds) === 2) {
				$user = User::findByCredentials($creds[0], $creds[1]);

				if (!empty($user)) {
					$request->attachUser($user);
					$next();
				}
			}
		}

		// Look at the active session for a user.
	}

}