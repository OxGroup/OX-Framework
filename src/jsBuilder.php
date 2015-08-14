<?php
/**
 * Created by OxProfitGroup.
 * User: aliaxander
 * Date: 21.05.15
 * Time: 15:32
 */

/**
 * Class jsBuilder
 */
namespace Ox;
class jsBuilder
{
    /**
     * @param $file
     * @param $txt
     * @return bool
     */
    public function put($file, $txt)
    {
        global $proj_global_cache_time;
        if ($txt != "") {
            $cache_time = $proj_global_cache_time; // Время жизни кэша в секундах
            $file = str_replace("/", ".", $file);
            $cache_file = "../cache/js/$file.cache";
            if (file_exists($cache_file)) {
                if ((time() - $cache_time) < filemtime($cache_file)) {
                } else {
                    $this->writeFile($file, $txt);
                }
            } else {
                $this->writeFile($file, $txt);
            }
        }
        return true;
    }

    /**
     * @param $file
     * @param $txt
     */
    private function writeFile($file, $txt)
    {
        $cache_file = "../cache/js/$file.cache";
        $handle = fopen($cache_file, 'w');
        fwrite($handle, $txt);
        fclose($handle);
    }

}