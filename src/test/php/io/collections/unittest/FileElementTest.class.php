<?php namespace io\collections\unittest;

use io\collections\FileElement;
use io\Path;
use io\File;
use lang\System;
use util\Date;

class FileElementTest extends \unittest\TestCase {
  private static $file;

  #[@beforeClass]
  public static function createTempFile() {
    self::$file= tempnam(System::tempDir(), 'fe-test');
  }

  #[@afterClass]
  public static function removeTempFile() {
    unlink(self::$file);
  }

  /** @return var[][] */
  private function arguments() {
    return [
      [self::$file],
      [new File(self::$file)],
      [new Path(self::$file)]
    ];
  }

  #[@test, @values('arguments')]
  public function can_create($arg) {
    new FileElement($arg);
  }

  #[@test, @values('arguments')]
  public function uses_realpath($arg) {
    $this->assertEquals(realpath(self::$file), (new FileElement($arg))->getURI());
  }

  #[@test, @ignore('Weird version messup')]
  public function equals_other_collection_with_same_path() {
    $this->assertEquals(new FileElement(self::$file), new FileElement(self::$file));
  }

  #[@test]
  public function name() {
    $this->assertEquals(basename(self::$file), (new FileElement(self::$file))->getName());
  }

  #[@test]
  public function size() {
    $this->assertEquals(filesize(self::$file), (new FileElement(self::$file))->getSize());
  }

  #[@test]
  public function created() {
    $this->assertEquals(new Date(filectime(self::$file)), (new FileElement(self::$file))->createdAt());
  }

  #[@test]
  public function modified() {
    $this->assertEquals(new Date(filemtime(self::$file)), (new FileElement(self::$file))->lastModified());
  }

  #[@test]
  public function accessed() {
    $this->assertEquals(new Date(fileatime(self::$file)), (new FileElement(self::$file))->lastAccessed());
  }
}