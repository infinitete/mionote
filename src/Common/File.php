<?php
/**
 * Created by PhpStorm.
 * User: mio
 * Date: 18-4-16
 * Time: 下午6:32
 */

namespace Mionote\Common;


class File
{
    const  CONFIG_PATH = '.mionote';
    const  TOKEN_FILE  = 'authinfo';

     public static function getHome(): string
     {
         return $_SERVER['HOME'] ??  '';
     }

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

     public static function readToken(): string
     {
         $file_path = self::getHome() . '/' .self::CONFIG_PATH . '/' . self::TOKEN_FILE;

         return file_get_contents($file_path) ?? '';
     }
}