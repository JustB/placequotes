<?php


namespace AppBundle\Wrapper;

use Doctrine\Common\Cache\CacheProvider;

class Curl
{
    protected $error_number = 0;
    protected $error_message = '';

    public function __construct(CacheProvider $cache)
    {
        $this->cache = $cache;
    }

    public function get($url, $cache_result = true)
    {
        $results = $this->cache->fetch(md5($url));
        if ($cache_result && ( $results !== false )) {

        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);

            $results             = curl_exec($ch);
            $this->error_number  = (int) curl_errno($ch);
            $this->error_message = curl_error($ch);

            curl_close($ch);

            $this->cache->save(md5($url), $results);
        }


        return $results;
    }

    public function getErrorNumber()
    {
        return $this->error_number;
    }

    public function getErrorMessage()
    {
        return $this->error_message;
    }

    public function hasError()
    {
        return $this->error_number !== 0;
    }

}