<?php

/**
 * Instantiate Redis.
 * We don't instantiate Redis directly so that we can make it easier to provide drop in DB replacements.
 * 
 * @return  object  Redis DB object
 */
function comicjet_db() {
	return new Redis_DB();
}


/**
 * Redis Database API class.
 *
 * Based on WP Redis (http://github.com/alleyinteractive/wp-redis/) by Matthew Boynes, Alley Interactive (http://www.alleyinteractive.com/)
 *
 * @author  Ryan Hellyer <ryanhellyer@gmail.com>
 */
class Redis_DB {

	/**
	 * Holds the cached objects
	 *
	 * @var array
	 * @access private
	 */
	var $cache = array();

	/**
	 * The amount of times the cache data was already stored in the cache.
	 *
	 * @access private
	 * @var int
	 */
	var $cache_hits = 0;

	/**
	 * Amount of times the cache did not have the request in cache
	 *
	 * @var int
	 * @access public
	 */
	var $cache_misses = 0;

	/**
	 * List of global groups
	 *
	 * @var array
	 * @access protected
	 */
	var $global_groups = array();

	/**
	 * List of non-persistent groups
	 *
	 * @var array
	 * @access protected
	 */
	var $non_persistent_groups = array();

	/**
	 * Adds data to the cache if it doesn't already exist.
	 *
	 * @uses Redis_DB::_exists Checks to see if the cache already has data.
	 * @uses Redis_DB::set Sets the data after the checking the cache
	 *		contents existence.
	 *
	 * @param int|string $key What to call the contents in the cache
	 * @param mixed $data The contents to store in the cache
	 * @param string $group Where to group the cache contents
	 * @param int $expire When to expire the cache contents
	 * @return bool False if cache key and group already exist, true on success
	 */
	public function add( $key, $data, $group = 'default', $expire = 0 ) {

		if ( $this->_exists( $this->_key( $key, $group ) ) )
			return false;

		return $this->set( $key, $data, $group, (int) $expire );
	}

	/**
	 * Sets the list of global groups.
	 *
	 * @param array $groups List of groups that are global.
	 */
	public function add_global_groups( $groups ) {
		$groups = (array) $groups;

		$groups = array_fill_keys( $groups, true );
		$this->global_groups = array_merge( $this->global_groups, $groups );
	}

	/**
	 * Sets the list of non-persistent groups.
	 *
	 * @param array $groups List of groups that are non-persistent.
	 */
	public function add_non_persistent_groups( $groups ) {
		$groups = (array) $groups;

		$groups = array_fill_keys( $groups, true );
		$this->non_persistent_groups = array_merge( $this->non_persistent_groups, $groups );
	}

	/**
	 * Remove the contents of the cache key in the group
	 *
	 * If the cache key does not exist in the group and $force parameter is set
	 * to false, then nothing will happen. The $force parameter is set to false
	 * by default.
	 *
	 * @param int|string $key What the contents in the cache are called
	 * @param string $group Where the cache contents are grouped
	 * @param bool $force Optional. Whether to force the unsetting of the cache
	 *		key in the group
	 * @return bool False if the contents weren't deleted and true on success
	 */
	public function delete( $key, $group = 'default', $force = false ) {
		$id = $this->_key( $key, $group );

		if ( ! $force && ! $this->_exists( $id ) )
			return false;

		if ( $this->_should_persist( $group ) ) {
			$result = $this->redis->delete( $id );
			if ( 1 != $result ) {
				return false;
			}
		}

		unset( $this->cache[ $id ] );
		return true;
	}

	/**
	 * Retrieves the cache contents, if it exists
	 *
	 * The contents will be first attempted to be retrieved by searching by the
	 * key in the cache group. If the cache is hit (success) then the contents
	 * are returned.
	 *
	 * On failure, the number of cache misses will be incremented.
	 *
	 * @param int|string $key What the contents in the cache are called
	 * @param string $group Where the cache contents are grouped
	 * @param string $force Whether to force a refetch rather than relying on the local cache (default is false)
	 * @return bool|mixed False on failure to retrieve contents or the cache
	 *		contents on success
	 */
	public function get( $key, $group = 'default', $force = false, &$found = null ) {
		$id = $this->_key( $key, $group );

		if ( $this->_exists( $id ) ) {
			$found = true;
			$this->cache_hits += 1;

			if ( $this->_should_persist( $group ) && ( $force || ( ! isset( $this->cache[ $id ] ) && ! array_key_exists( $id, $this->cache ) ) ) ) {
				$this->cache[ $id ] = $this->redis->get( $id );
				if ( ! is_numeric( $this->cache[ $id ] ) ) {
					$this->cache[ $id ] = unserialize( $this->cache[ $id ] );
				}
			}

			if ( is_object( $this->cache[ $id ] ) )
				return clone $this->cache[ $id ];
			else
				return $this->cache[ $id ];
		}

		$found = false;
		$this->cache_misses += 1;
		return false;
	}

	/**
	 * Replace the contents in the cache, if contents already exist
	 * @see Redis_DB::set()
	 *
	 * @param int|string $key What to call the contents in the cache
	 * @param mixed $data The contents to store in the cache
	 * @param string $group Where to group the cache contents
	 * @param int $expire When to expire the cache contents
	 * @return bool False if not exists, true if contents were replaced
	 */
	public function replace( $key, $data, $group = 'default', $expire = 0 ) {
		if ( ! $this->_exists( $this->_key( $key, $group ) ) )
			return false;

		return $this->set( $key, $data, $group, (int) $expire );
	}

	/**
	 * Sets the data contents into the cache
	 *
	 * The cache contents is grouped by the $group parameter followed by the
	 * $key. This allows for duplicate ids in unique groups. Therefore, naming of
	 * the group should be used with care and should follow normal function
	 * naming guidelines outside of core WordPress usage.
	 *
	 * The $expire parameter is not used, because the cache will automatically
	 * expire for each time a page is accessed and PHP finishes. The method is
	 * more for cache plugins which use files.
	 *
	 * @param int|string $key What to call the contents in the cache
	 * @param mixed $data The contents to store in the cache
	 * @param string $group Where to group the cache contents
	 * @param int $expire TTL for the data, in seconds
	 * @return bool Always returns true
	 */
	public function set( $key, $data, $group = 'default', $expire = 0 ) {
		$id = $this->_key( $key, $group );

		if ( is_object( $data ) )
			$data = clone $data;

		$this->cache[ $id ] = $data;

		if ( $this->_should_persist( $group ) ) {
			# If this is an integer, store it as such. Otherwise, serialize it.
			if ( ! is_numeric( $data ) || intval( $data ) != $data ) {
				$data = serialize( $data );
			}

			if ( empty( $expire ) ) {
				$this->redis->set( $id, $data );
			} else {
				$this->redis->setex( $id, $expire, $data );
			}
		}

		return true;
	}

	/**
	 * Echoes the stats of the caching.
	 *
	 * Gives the cache hits, and cache misses. Also prints every cached group,
	 * key and the data.
	 */
	public function stats() {
		echo "<p>";
		echo "<strong>Cache Hits:</strong> {$this->cache_hits}<br />";
		echo "<strong>Cache Misses:</strong> {$this->cache_misses}<br />";
		echo "</p>";
		echo '<ul>';
		foreach ( $this->cache as $group => $cache ) {
			echo "<li><strong>Group:</strong> $group - ( " . number_format( strlen( serialize( $cache ) ) / 1024, 2 ) . 'k )</li>';
		}
		echo '</ul>';
	}

	/**
	 * Replaces a key if it exists, otherwise adds it.
	 * 
	 * 
	 * Written by Ryan.
	 *
	 */
	public function write( $key, $data, $group = '' ) {
		if ( $this->_exists( $this->_key( $key, $group ) ) ) {
			$this->replace( $key, $data, $group );
		} else {
			$this->add( $key, $data, $group );
		}
	}

	/**
	 * Utility function to determine whether a key exists in the cache.
	 *
	 * @access protected
	 */
	protected function _exists( $id ) {
		if ( isset( $this->cache[ $id ] ) || array_key_exists( $id, $this->cache ) ) {
			return true;
		} else {
			return $this->redis->exists( $id );
		}
	}

	/**
	 * Utility function to generate the redis key for a given key and group.
	 *
	 * @param  string $key   The cache key.
	 * @param  string $group The cache group.
	 * @return string        A properly prefixed redis cache key.
	 */
	protected function _key( $key, $group = 'default' ) {
		if ( empty( $group ) ) {
			$group = 'default';
		}

		return preg_replace( '/\s+/', '', $group . ':' . $key );
	}

	/**
	 * Does this group use persistent storage?
	 *
	 * @param  string $group Cache group.
	 * @return bool        true if the group is persistent, false if not.
	 */
	protected function _should_persist( $group ) {
		return empty( $this->non_persistent_groups[ $group ] );
	}

	/**
	 * Sets up object properties; PHP 5 style constructor
	 *
	 * @return null|Redis_DB If cache is disabled, returns null.
	 */
	public function __construct() {

		// Server config
		$redis_server = array( 'host' => '127.0.0.1', 'port' => 6379 );

		$this->redis = new Redis();
		$this->redis->connect( $redis_server['host'], $redis_server['port'], 1, NULL, 100 ); # 1s timeout, 100ms delay between reconnections
		if ( ! empty( $redis_server['auth'] ) ) {
			$this->redis->auth( $redis_server['auth'] );
		}

		/**
		 * @todo This should be moved to the PHP4 style constructor, PHP5
		 * already calls __destruct()
		 */
		register_shutdown_function( array( $this, '__destruct' ) );
	}

	/**
	 * Will save the object cache before object is completely destroyed.
	 *
	 * Called upon object destruction, which should be when PHP ends.
	 *
	 * @return bool True value. Won't be used by PHP
	 */
	public function __destruct() {
		return true;
	}

}
