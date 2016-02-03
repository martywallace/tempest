<?php namespace Tempest\Extensions;

use Exception;
use Tempest\Utils\StringUtil;
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
		return array(
			// Bind the application to Twig templates.
			'app' => app()
		);
	}

	public function getFilters() {
		return array(
			new Twig_SimpleFilter('sha1', 'sha1'),
			new Twig_SimpleFilter('slugify', array(StringUtil::class, 'slugify')),
			new Twig_SimpleFilter('pluck', array($this, 'pluck'))
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
	 * Returns an array of plucked values. The values are plucked from an array of nested arrays or objects using their
	 * keys or properties.
	 *
	 * @param array $values An array of arrays or objects to pluck properties from.
	 * @param string $property The property or key to pluck from each item.
	 *
	 * @return array
	 *
	 * @throws Exception If the provided value is not an array.
	 */
	public function pluck(array $values, $property) {
		if (is_array($values)) {
			$result = array();

			foreach ($values as $value) {
				if (is_array($value) && array_key_exists($property, $value)) $result[] = $value[$property];
				if (is_object($value) && property_exists($value, $property)) $result[] = $value->{$property};
			}

			return $result;
		} else {
			throw new Exception('pluck() expects an array.');
		}
	}

}