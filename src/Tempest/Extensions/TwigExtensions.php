<?php namespace Tempest\Extensions;

use Tempest\Tempest;
use Tempest\Utils\StringUtil;
use Tempest\Utils\ObjectUtil;
use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleFilter;

/**
 * This class attaches Tempest level extensions to Twig.
 *
 * @package Tempest\Extensions
 * @author Marty Wallace
 */
class TwigExtensions extends Twig_Extension {

	public function getName() { return 'TempestTwigExtensions'; }

	public function getGlobals() {
		return [
			// Bind the application to Twig templates.
			'app' => Tempest::get()
		];
	}

	public function getFilters() {
		return [
			new Twig_SimpleFilter('sha1', 'sha1'),
			new Twig_SimpleFilter('slugify', [StringUtil::class, 'slugify']),
			new Twig_SimpleFilter('pluck', [ObjectUtil::class, 'pluck'])
		];
	}

	public function getFunctions() {
		return [
			new Twig_SimpleFunction('link', [$this, 'link'])
		];
	}

	/**
	 * Creates an absolute link relative to {@link App::public public} path.
	 *
	 * @param string $value The link relative to the public site URL.
	 * @param bool $full Whether or not to include the full site URL at the front of the result.
	 *
	 * @return string
	 */
	public function link($value, $full = false) {
		return ($full ? Tempest::get()->url : Tempest::get()->public) . '/' . ltrim($value, '/');
	}

}