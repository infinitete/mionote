<?php
/**
 * Created by PhpStorm.
 * User: mio
 * Date: 18-4-16
 * Time: 下午7:10
 */

namespace Mionote\Note;

use Evernote\Client as EvernoteClient;
use Mionote\Common\File;

class Client
{
    const SANDBOX = true;

    public static function  getCleint()
    {
        $token = File::readToken();

        if ($token == '') {
            return null;
        }

        return new EvernoteClient($token, self::SANDBOX);
    }
}