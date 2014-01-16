<?php

namespace WPModel;

class User
{
	// gettables
	static protected $_gettables = array(
		'meta',
		'exists'
	);

	// create
	static public function create($user = null)
	{
		return new static($user);
	}



	// props
	protected $_id = null;
	protected $_user = null;
	protected $_meta = null;



	// init
	function __construct($user = null)
	{
		if (is_null($user)) {
			$this->_id = get_current_user_id();
		} else if (is_int($user) || is_string($user)) {
			$this->_id = intval($user);
		} else {
			$this->_id = $user->ID;
			$this->_user = $user;
		}
	}



	// overload
	public function __get($name)
	{
		$this->_initUser();

		if (strtolower($name) === 'id') {
			return $this->_id;
		} else if ($name === 'user') {
			return $this->_user;
		} else if (in_array($name, static::$_gettables)) {
			return call_user_func_array(array($this, "_$name"), array());
		} else if (isset($this->_user->$name)) {
			return $this->_user->$name;
		} else {
			return null;
		}
	}

	public function __isset($name)
	{
		$this->_initPost();

		return (
			strtolower($name) === 'id' ||
			$name === 'user' ||
			in_array($name, static::$_gettables) ||
			isset($this->_user->$name)
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



	// init user
	protected function _initUser()
	{
		if (!isset($this->_user) && $this->_id) {
			$this->_user = get_user_by('id', $this->_id);
		}
	}



	// method properties
	protected function _meta()
	{
		if (!isset($this->_meta)) {
			$this->_meta = new UserMeta($this->id);
		}
		return $this->_meta;
	}



	// is
	protected function _exists()
	{
		return !!$this->user;
	}
}
