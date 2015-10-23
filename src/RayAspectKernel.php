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
 * Copyright (c) 2015 Yuuki Takezawa
 *
 *
 * CodeGenMethod Class, CodeGen Class is:
 * Copyright (c) 2012-2015, The Ray Project for PHP
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

namespace Ytake\LaravelAspect;

use Ray\Aop\Bind;
use Ray\Aop\Compiler;
use Ray\Aop\Matcher;
use PhpParser\Parser;
use PhpParser\Lexer;
use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter\Standard;
use Illuminate\Contracts\Container\Container;
use Ytake\LaravelAspect\Exception\ClassNotFoundException;

/**
 * Class RayAspectKernel
 */
class RayAspectKernel implements AspectDriverInterface
{
    /** @var Container */
    protected $app;

    /** @var array */
    protected $configure;

    /** @var Compiler  */
    protected $compiler;

    /** @var Bind  */
    protected $bind;

    /**
     * @param Container $app
     * @param Bind      $bind
     * @param array     $configure
     */
    public function __construct(Container $app, Bind $bind, array $configure)
    {
        $this->app = $app;
        $this->configure = $configure;
        $this->compiler = $this->getCompiler();
        $this->bind = $bind;
    }

    /**
     * @param null $module
     * @throws ClassNotFoundException
     */
    public function register($module = null)
    {
        if (!class_exists($module)) {
            throw new ClassNotFoundException($module);
        }
        (new $module($this->app, $this->bind))->setCompiler($this->compiler)->attach();
    }

    /**
     * @return Compiler
     */
    protected function getCompiler()
    {
        return new Compiler($this->configure['cache_dir'], new CodeGen(
            new Parser(new Lexer()),
            new BuilderFactory(),
            new Standard()
        ));
    }
}
