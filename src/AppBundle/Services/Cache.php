<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 26.06.17
 * Time: 15:53
 */

namespace AppBundle\Services;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * Class Cache
 * @package AppBundle\Services
 */
class Cache
{
    /**
     * @var FilesystemAdapter
     */
    private $cache;
    private $env;

    private const LIFETIME = 2 * 60; //seconds

    /**
     * Cache constructor.
     */
    public function __construct($env)
    {
        $this->env = $env;
        $this->cache = new FilesystemAdapter('', self::LIFETIME);
    }

    /**
     * @param Request $request
     * @return mixed|null
     */
    public function get(Request $request)
    {
        if ($this->env == 'dev') {
            return null;
        }

        $item = $this->cache->getItem($this->getKey($request));

        if (!$item->isHit()) {
            return null;
        }

        return $item->get();
    }

    /**
     * @param Request $request
     * @param $data
     */
    public function set(Request $request, $data) : void
    {
        $item = $this->cache->getItem($this->getKey($request));
        $item->set($data);
        $this->cache->save($item);
    }

    public function clear()
    {
        $this->cache->clear();
    }

    /**
     * @param Request $request
     * @return string
     */
    private function getKey(Request $request)
    {
        $key = $request->headers->get('API-AUTH-TOKEN') .
            $request->headers->get('DEVICE-ID') .
            $request->headers->get('APP-ID') .
            $request->getPathInfo();

        foreach ($request->query->all() as $param) {
            $key = $key . $param;
        }

        return md5($key);
    }
}