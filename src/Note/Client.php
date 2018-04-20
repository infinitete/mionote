<?php

namespace Mionote\Note;

use Evernote\Client as EvernoteClient;
use Mionote\Common\File;

class Client
{
    const SANDBOX = true;

    /**
     * Get EvernoteClient with User's token
     *
     * @return \Evernote\Client || null
     */
    public static function  getCleint()
    {
        $token = File::readToken();

        if ($token == '') {
            return null;
        }

        return new EvernoteClient($token, self::SANDBOX);
    }
}
