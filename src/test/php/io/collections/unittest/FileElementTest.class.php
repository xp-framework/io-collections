<?php namespace io\collections\unittest;

use io\collections\FileElement;
use io\{File, Path};
use lang\System;
use unittest\{AfterClass, BeforeClass, Ignore, Test, Values};
use util\Date;

class FileElementTest extends \unittest\TestCase {
  private static $file;

  #[BeforeClass]
  public static function createTempFile() {
    self::$file= tempnam(System::tempDir(), 'fe-test');
  }

  #[AfterClass]
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

  #[Test, Values('arguments')]
  public function can_create($arg) {
    new FileElement($arg);
  }

  #[Test, Values('arguments')]
  public function uses_realpath($arg) {
    $this->assertEquals(realpath(self::$file), (new FileElement($arg))->getURI());
  }

  #[Test, Ignore('Weird version messup')]
  public function equals_other_collection_with_same_path() {
    $this->assertEquals(new FileElement(self::$file), new FileElement(self::$file));
  }

  #[Test]
  public function name() {
    $this->assertEquals(basename(self::$file), (new FileElement(self::$file))->getName());
  }

  #[Test]
  public function size() {
    $this->assertEquals(filesize(self::$file), (new FileElement(self::$file))->getSize());
  }

  #[Test]
  public function created() {
    $this->assertEquals(new Date(filectime(self::$file)), (new FileElement(self::$file))->createdAt());
  }

  #[Test]
  public function modified() {
    $this->assertEquals(new Date(filemtime(self::$file)), (new FileElement(self::$file))->lastModified());
  }

  #[Test]
  public function accessed() {
    $this->assertEquals(new Date(fileatime(self::$file)), (new FileElement(self::$file))->lastAccessed());
  }
}