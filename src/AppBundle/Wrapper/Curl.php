<?php


namespace AppBundle\Wrapper;


class Curl
{
    protected $error_number = 0;
    protected $error_message = '';

    public function get($url, $cache_result = true)
    {
//        $cache_file = 'cache'.DIRECTORY_SEPARATOR.md5($url);
//
//        if ($cache_result && file_exists($cache_file)) {
//            $fh      = fopen($cache_file, 'r');
//            $results = fread($fh, filesize($cache_file));
//            fclose($fh);
//        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);

            $results             = curl_exec($ch);
            $this->error_number  = (int) curl_errno($ch);
            $this->error_message = curl_error($ch);

            curl_close($ch);

//            $fh = fopen($cache_file, 'w');
//            fwrite($fh, $results);
//            fclose($fh);
//        }


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