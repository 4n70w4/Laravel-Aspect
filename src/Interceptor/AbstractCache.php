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

namespace Ytake\LaravelAspect\Interceptor;

use Ray\Aop\MethodInvocation;
use Ray\Aop\MethodInterceptor;
use Ytake\LaravelAspect\Annotation\AnnotationReaderTrait;

/**
 * Class AbstractCache
 */
abstract class AbstractCache implements MethodInterceptor
{
    use AnnotationReaderTrait;

    /** @var string */
    protected $join = ":";

    /**
     * @param                  $name
     * @param MethodInvocation $invocation
     *
     * @return array
     */
    protected function generateCacheName($name, MethodInvocation $invocation)
    {
        if (is_array($name)) {
            throw new \InvalidArgumentException('Invalid argument');
        }
        if (is_null($name)) {
            $name = $invocation->getMethod()->name;
        }

        return [$name];
    }

    /**
     * @param MethodInvocation $invocation
     * @param                  $annotation
     * @param                  $keys
     *
     * @return array
     */
    protected function detectCacheKeys(MethodInvocation $invocation, $annotation, $keys)
    {
        $arguments = $invocation->getArguments();
        foreach ($invocation->getMethod()->getParameters() as $parameter) {
            // exclude object
            if (in_array('#' . $parameter->name, $annotation->key)) {
                if (!is_object($arguments[$parameter->getPosition()])) {
                    $keys[] = $arguments[$parameter->getPosition()];
                }
            }
        }

        return $keys;
    }

    /**
     * @param $annotation
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function detectCacheRepository($annotation)
    {
        $cache = app('cache')->store($annotation->driver);
        if (count($annotation->tags)) {
            $cache = $cache->tags($annotation->tags);

            return $cache;
        }

        return $cache;
    }
}
