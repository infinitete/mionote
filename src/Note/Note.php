<?php

namespace Mionote\Note;

use Evernote\Model\Notebook;

class Note
{
    /**
     * Find Notes by title
     *
     * @param string $title
     *
     * @return array
     */
    public static function find(string $title): array
    {
        $client = Client::getCleint();

        /**
         * The search string
         */
        $search = new \Evernote\Model\Search($title);
        /**
         * The notebook to search in
         */
        $notebook =  new Notebook();
        /**
         * The scope of the search
         */
//        $scope = \Evernote\Client::SEARCH_SCOPE_BUSINESS;
        $scope = \Evernote\Client::SEARCH_SCOPE_ALL;
        /**
         * The order of the sort
         */
        $order = \Evernote\Client::SORT_ORDER_NORMAL | \Evernote\Client::SORT_ORDER_RECENTLY_CREATED;
        /**
         * The number of results
         */
        $maxResult = 10;
        $results = $client->findNotesWithSearch($search, $notebook, $scope, $order, $maxResult);

        $notes = [];

        foreach ($results as $result) {

            $noteGuid    = $result->guid;
            $noteType    = $result->type;
            $noteTitle   = $result->title;
            $noteCreated = $result->created;
            $noteUpdated = $result->updated;

            array_push($notes, [
                'guid'  => $noteGuid,
                'type'  => $noteType,
                'title' => $noteTitle,
                'created_at' => $noteCreated,
                'updated_at' => $noteUpdated
            ]);

        }

        return $notes;
    }

    /**
     * Get a note by GUID
     *
     * @param string $guid
     *
     * @return \Evernote\Model\Note
     */
    public static function getNote(string  $guid): \Evernote\Model\Note
    {
        $client = Client::getCleint();

        return $client->getNote($guid);
    }

    /**
     * Create a note
     *
     * @param string $title
     * @param string $content
     * @param Notebook $notebook
     * @param array  $tags
     *
     * @return bool
     */
    public static function createNote(string $title, string $content, Notebook $notebook, array $tags = [])
    {
        $note = new \Evernote\Model\Note();

        $note->setTitle($title);
        $note->setContent(new \Evernote\Model\PlainTextNoteContent($content));

        if (count($tags) > 0) {
            $note->setTagNames($tags);
        }

        // TODO

        $client = Client::getCleint();

        try {
            $client->uploadNote($note, $notebook);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete a note from Evernote
     *
     * @param \Evernote\Model\Note $note
     */
    public static function deleteNote(\Evernote\Model\Note $note)
    {
        $client = Client::getCleint();

        $client->deleteNote($note);
    }
}
