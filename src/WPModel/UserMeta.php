<?php

namespace WPModel;

class UserMeta implements \ArrayAccess
{
  // props
  protected $_id = null;
  protected $_show_id = null;



  // init
  function __construct($user, $show_id = false)
  {
    if ($user instanceof \WP_User || $user instanceof User) {
      $this->_id = $user->ID;
    } else {
      $this->_id = intval($user);
    }
    $this->_show_id = $show_id;
  }



  // overload
  public function __get($name)
  {
    if ($this->_show_id && $name === 'id') {
      return $this->_id;
    }
    return get_user_meta($this->_id, $name, true);
  }

  public function __set($name, $value)
  {
    if ($this->_show_id && $name === 'id') {
      return;
    }
    update_user_meta($this->_id, $name, $value);
  }

  public function __isset($name)
  {
    if ($this->_show_id && $name === 'id') {
      return true;
    }
    return array_key_exists($name, get_user_meta($this->_id));
  }

  public function __unset($name)
  {
    if ($this->_show_id && $name === 'id') {
      return;
    }
    delete_user_meta($this->_id, $name);
  }

  // array access
  public function offsetGet($offset)
  {
    return $this->__get($offset);
  }

  public function offsetSet($offset, $value)
  {
    $this->__set($offset, $value);
  }

  public function offsetExists($offset)
  {
    return $this->__isset($offset);
  }

  public function offsetUnset($offset)
  {
    $this->__unset($offset);
  }
}
