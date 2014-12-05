<?php namespace Tempest\Social;

/**
 * Utilities for social sharing.
 * @author Marty Wallace.
 */
class Sharing
{

	const FACEBOOK_SHARE_ENDPOINT = 'https://facebook.com/dialog/feed';
	const TWITTER_SHARE_ENDPOINT = 'https://twitter.com/intent/tweet';


	/**
	 * Generate a Facebook share URL.
	 * @param $appId
	 * @param $link
	 * @param $picture
	 * @param $title
	 * @param $caption
	 * @param $description
	 * @param $redirectUrl
	 * @return string
	 */
	public static function getFacebookShareUrl($appId, $link, $picture, $title, $caption, $description, $redirectUrl)
	{
		$params = http_build_query(array(
			"app_id" => $appId,
			"link" => $link,
			"picture" => $picture,
			"name" => $title,
			"caption" => $caption,
			"description" => $description,
			"redirect_uri" => $redirectUrl
		));

		return self::FACEBOOK_SHARE_ENDPOINT . '?' . $params;
	}


	/**
	 * Generate a Twitter share URL.
	 * @param $text
	 * @return string
	 */
	public static function getTwitterShareUrl($text)
	{
		$params = http_build_query(array(
			"source" => "webclient",
			"text" => $text
		));

		return self::TWITTER_SHARE_ENDPOINT . '?' . $params;
	}

}