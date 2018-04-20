<?php

namespace Mionote\Common;


class File
{
    const  CONFIG_PATH = '.mionote';
    const  TOKEN_FILE  = 'authinfo';

    /**
     * Get User's HOME path
     *
     * @return string
     */
     public static function getHome(): string
     {
         return $_SERVER['HOME'] ??  '';
     }

    /**
     * Write User's API token to config file
     *
     * @param string $token
     *
     * @return bool
     */
     public static function writeToken($token): bool
     {
         $path = self::getHome() . '/' .self::CONFIG_PATH;

         if (is_dir($path) === false) {
             try {
                 @mkdir($path);
             } catch (\Exception $e) {
                 return false;
             }
         }

         $file = $path . '/' . self::TOKEN_FILE;

         return boolval(file_put_contents($file, $token));
     }

    /**
     * Read User's API token fron config file
     *
     * @return string
     */
     public static function readToken(): string
     {
         $file_path = self::getHome() . '/' .self::CONFIG_PATH . '/' . self::TOKEN_FILE;

         return file_get_contents($file_path) ?? '';
     }
}
