<?php

namespace Mionote\Note;

use Evernote\Model\Notebook as Model;
use EDAM\Types\Notebook as Type;


class Notebook
{

    /**
     * 即时列出所有的笔记本
     *
     * @param bool $refresh
     * @return array
     */
    public static function listNotebooks($refresh = true)
    {
        $client      = Client::getCleint();

        return $client->listNotebooks();
    }

    public static function getNotebook(string $name)
    {
        /** @var $notebook Model * */
        foreach (self::listNotebooks() as $notebook) {
            if (strtolower($notebook->getName()) === strtolower($name)) {
                return $notebook;
            }
        }

        return null;
    }

    /**
     * 创建一个笔记本
     *
     * @param string $name
     * @return bool
     */
    public static function createNotebook(string $name): bool
    {
        /** @var $notebook Model **/
        foreach (self::listNotebooks() as $notebook) {
            if (strtolower($notebook->getName()) === strtolower($name)) {
                return true;
            }
        }

        try {
            $client = Client::getCleint();
            $token  = $client->getToken();

            $store  = $client->getUserNotestore();

            $type = new Type(['name' => $name]);
            $store->createNotebook($token, $type);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}