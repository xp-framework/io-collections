<?php namespace io\collections\unittest;

use io\{Folder, Path};
use io\collections\FileCollection;
use util\Date;

class FileCollectionTest extends \unittest\TestCase {
  private static $dir;

  #[@beforeClass]
  public static function createTempFile() {
    self::$dir= realpath(getcwd());
  }

  /** @return var[][] */
  private function arguments() {
    return [
      [self::$dir],
      [new Folder(self::$dir)],
      [new Path(self::$dir)]
    ];
  }

  #[@test, @values('arguments')]
  public function can_create($arg) {
    new FileCollection($arg);
  }

  #[@test, @values('arguments')]
  public function uses_realpath_and_always_ends_with_directory_separator($arg) {
    $this->assertEquals(
      rtrim(self::$dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR,
      (new FileCollection($arg))->getURI()
    );
  }

  #[@test, @ignore('Weird version messup')]
  public function equals_other_collection_with_same_path() {
    $this->assertEquals(new FileCollection(self::$dir), new FileCollection(self::$dir));
  }

  #[@test]
  public function name() {
    $this->assertEquals(basename(self::$dir), (new FileCollection(self::$dir))->getName());
  }

  #[@test]
  public function size() {
    $this->assertEquals(filesize(self::$dir), (new FileCollection(self::$dir))->getSize());
  }

  #[@test]
  public function created() {
    $this->assertEquals(new Date(filectime(self::$dir)), (new FileCollection(self::$dir))->createdAt());
  }

  #[@test]
  public function modified() {
    $this->assertEquals(new Date(filemtime(self::$dir)), (new FileCollection(self::$dir))->lastModified());
  }

  #[@test]
  public function accessed() {
    $this->assertEquals(new Date(fileatime(self::$dir)), (new FileCollection(self::$dir))->lastAccessed());
  }
}