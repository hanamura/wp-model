<?php

namespace WPModel;

class Term
{
	// create
	static public function create($term, $taxonomy = null)
	{
		return new static($term, $taxonomy);
	}



	// props
	protected $_id = null;
	protected $_term = null;
	protected $_taxonomy = null;



	// init
	function __construct($term, $taxonomy = null)
	{
		if (is_int($term) || is_string($term)) {
			$this->_id = intval($term);
			$this->_taxonomy = $taxonomy;
		} else {
			$this->_id = intval($term->term_id);
			$this->_term = $term;
			$this->_taxonomy = $term->taxonomy;
		}
	}



	// overload
	public function __set($name, $value)
	{
		
	}

	public function __get($name)
	{
		$this->_initTerm();

		if (strtolower($name) === 'id') {
			return $this->_id;
		} else if ($name === 'term') {
			return $this->_term;
		} else if (isset($this->_term->$name)) {
			return $this->_term->$name;
		} else {
			return null;
		}
	}

	public function __isset($name)
	{
		$this->_initTerm();

		return strtolower($name) === 'id' || $name === 'term' || isset($this->_term->$name);
	}

	public function __unset($name)
	{
		
	}



	// init term
	protected function _initTerm()
	{
		if (!isset($this->_term)) {
			$this->_term = get_term($this->_id, $this->_taxonomy);
		}
	}
}
