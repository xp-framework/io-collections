<?php namespace io\collections;

use io\streams\FileInputStream;
use io\streams\FileOutputStream;
use io\File;
use io\Path;
use lang\Value;
use util\Date;

/**
 * Represents a file element
 *
 * @see   xp://io.collections.FileCollection
 * @test  xp://io.collections.unittest.FileElementTest
 */
class FileElement implements IOElement, Value {
  public $uri;
  protected $origin = null;

  /**
   * Constructor
   *
   * @param  string|io.File|io.Path $arg
   */
  public function __construct($arg) {
    if ($arg instanceof File) {
      $this->uri= $arg->getURI();
    } else if ($arg instanceof Path) {
      $this->uri= $arg->asRealpath()->toString();
    } else {
      $this->uri= realpath($arg);
    }
  }

  /**
   * Returns this element's name
   *
   * @return  string
   */
  public function getName() {
    return basename($this->uri);
  }

  /**
   * Returns this element's URI
   *
   * @return  string
   */
  public function getURI() { 
    return $this->uri;
  }

  /**
   * Retrieve this element's size in bytes
   *
   * @return  int
   */
  public function getSize() { 
    return filesize($this->uri);
  }

  /**
   * Retrieve this element's created date and time
   *
   * @return  util.Date
   */
  public function createdAt() {
    return new Date(filectime($this->uri));
  }

  /**
   * Retrieve this element's last-accessed date and time
   *
   * @return  util.Date
   */
  public function lastAccessed() {
    return new Date(fileatime($this->uri));
  }

  /**
   * Retrieve this element's last-modified date and time
   *
   * @return  util.Date
   */
  public function lastModified() {
    return new Date(filemtime($this->uri));
  }
  
  /** Creates a string representation of this object */
  public function toString() { 
    return nameof($this).'('.$this->uri.')';
  }

  /** Creates a string representation of this object */
  public function hashCode() { 
    return 'FE'.md5($this->uri);
  }

  /** Compares this to another value */
  public function compareTo($value) {
    return $value instanceof self ? strcmp($this->uri, $value->uri) : 1;
  }

  /**
   * Gets origin of this element
   *
   * @return  io.collections.IOCollection
   */
  public function getOrigin() {
    return $this->origin;
  }

  /**
   * Sets origin of this element
   *
   * @param   io.collections.IOCollection
   */
  public function setOrigin(IOCollection $origin) {
    $this->origin= $origin;
  }

  /**
   * Gets input stream to read from this element
   *
   * @deprecated Use in() instead
   * @return  io.streams.InputStream
   * @throws  io.IOException
   */
  public function getInputStream() {
    return $this->in();
  }

  /**
   * Gets output stream to read from this element
   *
   * @deprecated Use out() instead
   * @return  io.streams.OutputStream
   * @throws  io.IOException
   */
  public function getOutputStream() {
    return $this->out();
  }

  /**
   * Gets input stream to read from this element
   *
   * @return  io.streams.InputStream
   * @throws  io.IOException
   */
  public function in() {
    return new FileInputStream($this->uri);
  }

  /**
   * Gets output stream to read from this element
   *
   * @return  io.streams.OutputStream
   * @throws  io.IOException
   */
  public function out() {
    return new FileOutputStream($this->uri);
  }
} 
