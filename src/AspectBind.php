<?php

/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 *
 * Copyright (c) 2015-2016 Yuuki Takezawa
 *
 */
namespace Ytake\LaravelAspect;

use Ray\Aop\Bind;
use Illuminate\Filesystem\Filesystem;

/**
 * Class AspectBind
 */
class AspectBind
{
    /** @var bool */
    protected $cacheable;

    /** @var string */
    protected $path;

    /** @var Filesystem */
    protected $filesystem;

    /** @var string */
    protected $extension = '.cached.php';

    /**
     * AspectBind constructor.
     * @param Filesystem $filesystem
     * @param string $path
     * @param bool $cacheable
     */
    public function __construct(Filesystem $filesystem, $path, $cacheable = false)
    {
        $this->filesystem = $filesystem;
        $this->cacheable = $cacheable;
        $this->path = $path;
    }

    /**
     * @param $class
     * @param array $pointcuts
     * @return Bind
     */
    public function bind($class, array $pointcuts)
    {
        if (!$this->cacheable) {
            return (new Bind)->bind($class, $pointcuts);
        }
        $className = str_replace("\\", "_", $class);
        $filePath = $this->path . "/{$className}" . $this->extension;
        if (!$this->filesystem->exists($filePath)) {
            $this->makeCacheDir($this->path);
            $bind = (new Bind)->bind($class, $pointcuts);
            $this->filesystem->put($filePath, serialize($bind));
        }
        return unserialize(file_get_contents($filePath));
    }

    /**
     * @param $path
     */
    private function makeCacheDir($path)
    {
        if (!$this->filesystem->exists($path)) {
            $this->filesystem->makeDirectory($path, 0777, true);
        }
    }
}
