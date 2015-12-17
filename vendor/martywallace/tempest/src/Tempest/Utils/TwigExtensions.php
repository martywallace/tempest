<?php namespace Tempest\Utils;

use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleFilter;

/**
 * This class attaches Tempest level extensions to Twig.
 *
 * @package Tempest\Utils
 * @author Marty Wallace
 */
class TwigExtensions extends Twig_Extension {

	public function getName() { return 'TempestTwigExtensions'; }

	public function getGlobals() {
		return array(
			// Bind the application to Twig templates.
			'app' => app()
		);
	}

	public function getFilters() {
		return array(
			new Twig_SimpleFilter('sha1', 'sha1'),
			new Twig_SimpleFilter('hyphenate', array($this, 'hyphenate'))
		);
	}

	public function getFunctions() {
		return array(
			new Twig_SimpleFunction('link', array($this, 'link'))
		);
	}

	/**
	 * Creates an absolute link relative to the "url" configuration value.
	 *
	 * @param string $value The link relative to the public site URL.
	 *
	 * @return string
	 */
	public function link($value) {
		return app()->url . '/' . ltrim($value, '/');
	}

	/**
	 * Hyphenate some text, removing any non-word characters and replacing whitespace with hyphens e.g. "My name is
	 * John" becomes "my-name-is-john".
	 *
	 * @param string $value The input text.
	 *
	 * @return string
	 */
	public function hyphenate($value) {
		$base = preg_replace('/[^\w\s]+/', '', $value);
		$base = preg_replace('/\s+/', '-', $base);

		return strtolower($base);
	}

}