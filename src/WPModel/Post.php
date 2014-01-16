<?php

namespace WPModel;

class Post
{
	static protected $_gettables = array(
		'permalink',
		'children',
		'terms',
		'meta',
		'image',
		'images',
		'exists'
	);

	// create
	static public function create($post)
	{
		return new static($post);
	}



	// props
	protected $_id = null;
	protected $_post = null;
	protected $_permalink = null;
	protected $_children = null;
	protected $_meta = null;
	protected $_images = null;



	// init
	function __construct($post)
	{
		if (!$post) {
			$this->_id = null;
		} else if (is_int($post) || is_string($post)) {
			$this->_id = intval($post);
		} else {
			$this->_id = $post->ID;
			$this->_post = $post;
		}
	}



	// overload
	public function __get($name)
	{
		$this->_initPost();

		if (strtolower($name) === 'id') {
			return $this->_id;
		} else if ($name === 'post') {
			return $this->_post;
		} else if (in_array($name, static::$_gettables)) {
			return call_user_func_array(array($this, "_$name"), array());
		} else if (isset($this->_post->$name)) {
			return $this->_post->$name;
		} else {
			return null;
		}
	}

	public function __isset($name)
	{
		$this->_initPost();

		return (
			strtolower($name) === 'id' ||
			$name === 'post' ||
			in_array($name, static::$_gettables) ||
			isset($this->_post->$name)
		);
	}

	public function __call($name, $args)
	{
		if (in_array($name, static::$_gettables)) {
			return call_user_func_array(array($this, "_$name"), $args);
		} else {
			return null;
		}
	}



	// init post
	protected function _initPost()
	{
		if (!isset($this->_post) && $this->_id) {
			$this->_post = get_post($this->_id);
		}
	}



	// method properties
	protected function _permalink()
	{
		if (!isset($this->_permalink)) {
			$this->_permalink = get_permalink($this->id);
		}
		return $this->_permalink;
	}

	protected function _children($options = null)
	{
		if (!isset($this->_children)) {
			$children = get_children(array('post_parent' => $this->id));
			usort($children, function($a, $b) { return $a->menu_order - $b->menu_order; });
			$children = array_map(array('WPModel\Post', 'create'), $children);
			$this->_children = $children;
		}

		$children = $this->_children;

		// filter by post type
		if (isset($options['post_type'])) {
			$post_type = $options['post_type'];
			$children = array_filter($children, function($child) use($post_type) {
				if (is_array($post_type)) {
					return in_array($child->post_type, $post_type);
				} else {
					return $child->post_type === $post_type;
				}
			});
		}

		// filter by post mime type
		if (isset($options['post_mime_type'])) {
			$post_mime_type = $options['post_mime_type'];
			if (!is_array($post_mime_type)) {
				$post_mime_type = array($post_mime_type);
			}
			$children = array_filter($children, function($child) use($post_mime_type) {
				foreach ($post_mime_type as $type) {
					if ($child->matchMimeType($type)) {
						return true;
					}
				}
				return false;
			});
		}

		// filter by id
		if (isset($options['id'])) {
			$id = $options['id'];
			if (!is_array($id)) {
				$id = array($id);
			}
			$children = array_filter($children, function($child) use($id) {
				return in_array($child->id, $id);
			});
		}

		$children = array_values($children);

		return $children;
	}

	protected function _terms($options = null)
	{
		$taxonomy = isset($options['taxonomy']) ? $options['taxonomy'] : 'category';

		$terms = wp_get_post_terms($this->id, $taxonomy);
		$terms = array_map(array('WPModel\Term', 'create'), $terms);

		return $terms;
	}

	// meta
	protected function _meta()
	{
		if (!isset($this->_meta)) {
			$this->_meta = new PostMeta($this->id);
		}
		return $this->_meta;
	}

	// image source
	protected function _image($options = null)
	{
		$size = isset($options['size']) ? $options['size'] : 'full';

		if ($sources = $this->images) {
			return isset($sources[$size]) ? $sources[$size] : $sources['full'];
		} else {
			return null;
		}
	}

	protected function _images()
	{
		if (!isset($this->_images)) {
			$this->_images = array();
			$meta = wp_get_attachment_metadata($this->id);
			if ($meta) {
				$dir = wp_upload_dir();
				$this->_images['full'] = new Image(array(
					'url' => $full_url = $dir['baseurl'] . '/' . $meta['file'],
					'path' => $full_path = $dir['basedir'] . '/' . $meta['file'],
					'width' => $meta['width'],
					'height' => $meta['height']
				));
				foreach ($meta['sizes'] as $name => $info) {
					$this->_images[$name] = new Image(array(
						'url' => str_replace(wp_basename($full_url), $info['file'], $full_url),
						'path' => str_replace(wp_basename($full_path), $info['file'], $full_path),
						'width' => $info['width'],
						'height' => $info['height']
					));
				}
			}
		}
		return $this->_images;
	}



	// is
	protected function _exists()
	{
		return !!$this->post;
	}

	public function hasChild($post)
	{
		if (!($post instanceof Post) || !($post instanceof \WP_Post)) {
			$post = new Post($post);
		}
		return $post->post_parent === $this->id;
	}

	public function matchMimeType($query)
	{
		$mime_type = $this->post_mime_type;

		if ($mime_type === $query) {
			return true;
		} else {
			$index = strpos($mime_type, '/');
			return (
				is_int($index) &&
				(
					substr($mime_type, 0, $index) === $query ||
					substr($mime_type, $index + 1) === $query
				)
			);
		}
	}
}
