<?php namespace io\collections;

use io\Folder;
use io\Path;
use io\IOException;
use lang\Value;
use util\Date;
use util\NoSuchElementException;

/**
 * File collection
 *
 * @see   xp://io.collections.IOCollection
 * @test  xp://io.collections.unittest.FileCollectionTest
 */
class FileCollection implements IOCollection, RandomCollectionAccess, Value {
  public $uri;
  protected $origin = null;
  protected $_hd    = null;
    
  /**
   * Constructor
   *
   * @param  string|io.Folder|io.Path $arg
   */
  public function __construct($arg) {
    if ($arg instanceof Folder) {
      $this->uri= $arg->getURI();
    } else if ($arg instanceof Path) {
      $this->uri= $arg->asRealpath()->toString().DIRECTORY_SEPARATOR;
    } else {
      $this->uri= rtrim(realpath($arg), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
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
   * Open this collection
   *
   */
  public function open() { 
    $this->_hd= opendir($this->uri);
  }

  /**
   * Rewind this collection (reset internal pointer to beginning of list)
   *
   */
  public function rewind() { 
    rewinddir($this->_hd);
  }
  
  /**
   * Creates a qualified name
   *
   * @param   string
   * @return  string
   */
  protected function qualifiedName($name) {
    return $this->uri.basename($name);
  }

  /**
   * Retrieve next element in collection. Return NULL if no more entries
   * are available
   *
   * @return  io.collection.IOElement
   */
  public function next() { 
    do {
      if (false === ($entry= readdir($this->_hd))) return null;
    } while ('.' == $entry || '..' == $entry);
    
    $qualified= $this->uri.$entry; 
    if (is_dir($qualified)) {
      $next= new FileCollection($qualified);
    } else {
      $next= new FileElement($qualified);
    }
    $next->setOrigin($this);
    return $next;
  }

  /**
   * Close this collection
   *
   */
  public function close() { 
    closedir($this->_hd);
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
    return 'FC'.md5($this->uri);
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
   * @return  io.streams.InputStream
   * @throws  io.IOException
   */
  public function getInputStream() {
    throw new IOException('Cannot read from a directory');
  }

  /**
   * Gets output stream to read from this element
   *
   * @return  io.streams.OutputStream
   * @throws  io.IOException
   */
  public function getOutputStream() {
    throw new IOException('Cannot write to a directory');
  }

  /**
   * Creates a new element in this collection
   *
   * @param   string name
   * @return  io.collections.IOElement
   * @throws  io.OperationNotSupportedException
   * @throws  io.IOException
   */
  public function newElement($name) {
    $qualified= $this->qualifiedName($name);
    if (!touch($qualified)) {
      throw new IOException('Cannot create '.$qualified);
    }
    $created= new FileElement($qualified);
    $created->setOrigin($this);
    return $created;
  }

  /**
   * Creates a new collection inside this collection
   *
   * @param   string name
   * @return  io.collections.IOCollection
   * @throws  io.OperationNotSupportedException
   * @throws  io.IOException
   */
  public function newCollection($name) {
    $qualified= $this->qualifiedName($name);
    if (!mkdir($qualified)) {
      throw new \io\IOException('Cannot create '.$qualified);
    }
    $created= new FileCollection($qualified);
    $created->setOrigin($this);
    return $created;
  }

  /**
   * Finds an element inside this collection
   *
   * @param   string name
   * @return  io.collections.IOElement
   * @throws  io.OperationNotSupportedException
   * @throws  io.IOException
   */
  public function findElement($name) {
    $qualified= $this->qualifiedName($name);
    if (!is_file($qualified)) return null;

    $found= new FileElement($qualified);
    $found->setOrigin($this);
    return $found;
  }
  
  /**
   * Finds a collection inside this collection
   *
   * @param   string name
   * @return  io.collections.IOCollection
   * @throws  io.OperationNotSupportedException
   * @throws  io.IOException
   */
  public function findCollection($name) {
    $qualified= $this->qualifiedName($name);
    if (!is_dir($qualified)) return null;

    $found= new FileCollection($qualified);
    $found->setOrigin($this);
    return $found;
  }

  /**
   * Gets an element inside this collection
   *
   * @param   string name
   * @return  io.collections.IOElement
   * @throws  util.NoSuchElementException
   * @throws  io.OperationNotSupportedException
   * @throws  io.IOException
   */
  public function getElement($name) {
    if (!($found= $this->findElement($name))) {
      throw new NoSuchElementException('Cannot find '.$name.' in '.$this->uri);
    }
    return $found;
  }
  
  /**
   * Get a collection inside this collection
   *
   * @param   string name
   * @return  io.collections.IOCollection
   * @throws  util.NoSuchElementException
   * @throws  io.OperationNotSupportedException
   * @throws  io.IOException
   */
  public function getCollection($name) {
    if (!($found= $this->findCollection($name))) {
      throw new NoSuchElementException('Cannot find '.$name.' in '.$this->uri);
    }
    return $found;
  }

  /**
   * Removes an element from this collection
   *
   * @param   string name
   * @throws  io.OperationNotSupportedException
   * @throws  io.IOException
   */
  public function removeElement($name) {
    $qualified= $this->qualifiedName($name);
    if (!unlink($qualified)) {
      throw new IOException('Cannot remove '.$qualified);
    }
  }

  /**
   * Removes a collection from this collection
   *
   * @param   string name
   * @throws  io.OperationNotSupportedException
   * @throws  io.IOException
   */
  public function removeCollection($name) {
    $qualified= $this->qualifiedName($name);
    if (!rmdir($qualified)) {
      throw new IOException('Cannot remove '.$qualified);
    }
  }
} 
