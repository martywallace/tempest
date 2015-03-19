<?php

namespace Tempest\Utils;


/**
 * Wraps a path string and provides utilities for working with that path.
 *
 * @author Marty Wallace.
 */
class Path
{

	/**
	 * The delimiter used for paths.
	 */
	const DELIMITER = '/';


	/**
	 * Flag to retain the delimiters in the supplied path.
	 */
	const DELIMITER_RETAIN = 1;


	/**
	 * Flag to add a delimiter to the left side of the supplied path, and remove any from the right.
	 */
	const DELIMITER_LEFT = 2;


	/**
	 * Flag to add a delimiter to the right side of the supplied path, and remove any from the left.
	 */
	const DELIMITER_RIGHT = 3;


	/**
	 * Flag to add delimiters to the left and right sides of the supplied path.
	 */
	const DELIMITER_BOTH = 4;


	/**
	 * Flag to remove the delimiters from the left and right sides of the supplied path.
	 */
	const DELIMITER_NONE = 5;


	/**
	 * A regex pattern that matches one or more sequential forward or back slashes.
	 */
	const PATTERN_SLASHES = '/[\/\\\\]+/';


	/**
	 * A regex pattern that matches a file extension at the end of a path.
	 */
	const PATTERN_EXTENSION = '/\.(\w+)\/*$/';


	/**
	 * An alias for Path::__construct().
	 *
	 * @param string $path The base path, which will be normalized.
	 * @param int $strategy The desired strategy for dealing with leading and trailing delimiters in the result path.
	 *
	 * @return Path A new instance of Path.
	 */
	public static function create($path, $strategy = 1)
	{
		return new static($path, $strategy);
	}


	/**
	 * Normalizes a path, replacing forward and back slashes with a single forward slash.
	 *
	 * @param string $path The base path string.
	 * @param int $strategy The desired strategy for dealing with leading and trailing delimiters in the result path.
	 *
	 * @return string The normalized path string.
	 */
	public static function normalize($path, $strategy = 1)
	{
		$path = preg_replace(self::PATTERN_SLASHES, self::DELIMITER, $path);

		if ($strategy !== self::DELIMITER_RETAIN)
		{
			// Trim delimiters if we using any strategy other than DELIMITER_RETAIN.
			$path = trim($path, self::DELIMITER);
		}

		if (strlen($path) === 0)
		{
			if ($strategy === self::DELIMITER_LEFT ||
			   $strategy === self::DELIMITER_RIGHT ||
			   $strategy === self::DELIMITER_BOTH)
			{
				// The provided path was an empty string, but we want a delimiter at some point in the result.
				return self::DELIMITER;
			}
		}

		if ($strategy === self::DELIMITER_LEFT || $strategy === self::DELIMITER_BOTH) $path = self::DELIMITER . $path;
		if ($strategy === self::DELIMITER_RIGHT || $strategy === self::DELIMITER_BOTH) $path = $path . self::DELIMITER;

		return $path;
	}


	/**
	 * @var string
	 */
	protected $path;


	/**
	 * Constructor.
	 *
	 * @param string $path The base path, which will be normalized.
	 * @param int $strategy The desired strategy for dealing with leading and trailing delimiters in the result path.
	 */
	public function __construct($path = '', $strategy = 1)
	{
		$this->path = self::normalize($path, $strategy);
	}


	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->path;
	}


	/**
	 * Applies a new delimiter strategy to this path.
	 *
	 * @param int $strategy A new delimiter strategy.
	 *
	 * @return Path
	 */
	public function setStrategy($strategy)
	{
		$this->path = path::normalize($this->path, $strategy);
		return $this;
	}


	/**
	 * Trims delimiters from the left side of the path.
	 *
	 * @return Path
	 */
	public function ltrim()
	{
		$this->path = ltrim($this->path, self::DELIMITER);
		return $this;
	}


	/**
	 * Trims delimiters from the right side of the path.
	 *
	 * @return Path
	 */
	public function rtrim()
	{
		$this->path = rtrim($this->path, self::DELIMITER);
		return $this;
	}


	/**
	 * Trims delimiters from both sides of the path.
	 *
	 * @return Path
	 */
	public function trim()
	{
		$this->path = trim($this->path, self::DELIMITER);
		return $this;
	}


	/**
	 * Pads the left side of the path with a delimiter.
	 *
	 * @return Path
	 */
	public function lpad()
	{
		$this->ltrim();
		$this->path = self::DELIMITER . $this->path;

		return $this;
	}


	/**
	 * Pads the right side of the path with a delimiter.
	 *
	 * @return Path
	 */
	public function rpad()
	{
		$this->rtrim();
		$this->path = $this->path . self::DELIMITER;

		return $this;
	}


	/**
	 * Pads both sides of the path with a delimiter.
	 *
	 * @return Path
	 */
	public function pad()
	{
		$this->trim();
		$this->path = self::DELIMITER . $this->path . self::DELIMITER;

		return $this;
	}


	/**
	 * Appends a path to the end of this path.
	 *
	 * @param Path|string $path The path to append.
	 *
	 * @return Path
	 */
	public function append($path)
	{
		$this->path .= self::DELIMITER . $path;
		$this->path = self::normalize($this->path, self::DELIMITER_RETAIN);

		return $this;
	}


	/**
	 * Prepends a path to the front of this path.
	 *
	 * @param Path|string $path The path to prepend.
	 *
	 * @return Path
	 */
	public function prepend($path)
	{
		$this->path = $path . self::DELIMITER . $this->path;
		$this->path = self::normalize($this->path, self::DELIMITER_RETAIN);

		return $this;
	}


	/**
	 * Returns a new path relative to the target path.
	 *
	 * @param Path|string $path
	 *
	 * @return Path
	 */
	public function relativeTo($path)
	{
		if (empty($path)) return static::create($this);

		$target = static::create($path, Path::DELIMITER_RETAIN)->ltrim();
		$self = static::create($this->path, Path::DELIMITER_RETAIN)->ltrim();

		if (strlen($self->value()) > 0 && strlen($target->value()) > 0)
		{
			if (strpos($self->value(), $target->value()) === 0)
			{
				return static::create(substr($self, strlen($target)), self::DELIMITER_LEFT);
			}
		}

		return $self;
	}


	/**
	 * Returns an Array of the segments that make up this path.
	 *
	 * @return array
	 */
	public function segments()
	{
		if (strlen($this->path) === 0 || $this->path === self::DELIMITER)
		{
			// The path string is empty or the delimiter on its own.
			return array();
		}

		return explode(self::DELIMITER, trim($this->path, self::DELIMITER));
	}


	/**
	 * Returns the first segment.
	 *
	 * @return null|string
	 */
	public function first()
	{
		return $this->segment(0);
	}


	/**
	 * Returns the last segment.
	 *
	 * @return null|string
	 */
	public function last()
	{
		return $this->segment(count($this->segments()) - 1);
	}


	/**
	 * Returns a path segment at a given index.
	 *
	 * @param int $index The segment index.
	 *
	 * @return null|string
	 */
	public function segment($index)
	{
		if ($index < 0 || $index >= count($this->segments())) return null;

		$segments = $this->segments();
		return $segments[$index];
	}


	/**
	 * Returns the file extension associated with this Path, or null if one was not included. The file extension is any
	 * sequence of letters or numbers at the end of the path trailing a period (.), optionally followed by one or more
	 * forward slashes (/).
	 *
	 * @return string|null
	 */
	public function extension()
	{
		preg_match(self::PATTERN_EXTENSION, $this->path, $match);
		return count($match) === 0 ? null : $match[1];
	}


	/**
	 * Alias for basename(path).
	 *
	 * @return string
	 */
	public function basename()
	{
		return basename($this->path);
	}


	/**
	 * Alias for dirname(path). Note that the native dirname() method returns a single period (.) if the result was
	 * empty; in this case, the result is a single slash instead.
	 *
	 * @return string
	 */
	public function dirname()
	{
		$path = dirname($this->path);

		if (strlen($path) === 0 || $path === '.') $path = self::DELIMITER;

		return self::normalize($path);
	}


	/**
	 * Determine whether this path points to a valid file or directory.
	 *
	 * @return bool
	 */
	public function valid()
	{
		return is_file($this->path) || is_dir($this->path);
	}


	/**
	 * Alias for is_file(path).
	 *
	 * @return bool
	 */
	public function isFile()
	{
		return is_file($this->path);
	}


	/**
	 * Alias for is_dir(path).
	 *
	 * @return bool
	 */
	public function isDir()
	{
		return is_dir($this->path);
	}


	/**
	 * Alias for __toString().
	 *
	 * @return string
	 */
	public function value()
	{
		return $this->path;
	}

}