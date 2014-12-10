<?php

namespace WPModel;

class Image
{
  static protected $_gettables = array(
    'url',
    'path',
    'width',
    'height'
  );



  // create
  static public function create($source = null)
  {
    return new static($source);
  }



  // props
  protected $_url = null;
  protected $_path = null;
  protected $_width = null;
  protected $_height = null;



  // init
  function __construct($source = null)
  {
    $this->_url = isset($source['url']) ? $source['url'] : null;
    $this->_path = isset($source['path']) ? $source['path'] : null;
    $this->_width = isset($source['width']) ? $source['width'] : null;
    $this->_height = isset($source['height']) ? $source['height'] : null;
  }



  // overload
  public function __get($name)
  {
    if (in_array($name, static::$_gettables)) {
      $_name = "_$name";
      return $this->$_name;
    } else {
      return null;
    }
  }

  public function __isset($name)
  {
    return in_array($name, static::$_gettables);
  }
}
