<?php

namespace Mionote\Command\Note;

use Evernote\Enml\Converter\EnmlToHtmlConverter;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Mionote\Note\Note;

class Delete extends BaseCommand
{
    protected function configure() {
        $this->setName("note:delete")
            ->setDescription("Delete a note from evernote")
            ->setHelp("Useage: mionote note:delete title");

        $this->addArgument("title", InputArgument::REQUIRED, "Title of the note which you want to delete");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $title = $input->getArgument('title');

        $io = new SymfonyStyle($input, $output);

        $notes = Note::find($title);

        $count = count($notes);

        if ($count === 0) {
            $io->caution("Found No One about \"{$title}\"");
            return null;
        }

        $target = null;

        if ($count === 1) {
            $target = $notes[0];
        } else {
            $choices = [];
            $table   = [];

            $io->success("Find {$count} note(s)");

            foreach ($notes as $k => $note) {
                $created_at = date('Y-m-d H:i:s', $note['created_at'] / 1000);
                $updated_at = date('Y-m-d H:i:s', $note['updated_at'] / 1000);
                $choices[$k] = $k;

                array_push($table, [
                    'Sort' => $k,
                    'Title' => $note['title'],
                    'Created At' => $created_at,
                    'Updated At' => $updated_at,
                    'GUID'    => $note['guid']
                ]);
            }

            $io->table(['Sort', 'Title', 'Created At', 'Updated At', 'GUID'], $table);

            $choice = $io->choice("Choice：", $choices);

            $target = $notes[$choice];
        }

        if ($target === null) {
            return null;
        }

        $choice = $io->choice("Really want to delete \"{$target['title']}\"?", ['Yes' => 'Y', 'No' => 'N']);
        if ($choice === 'No') {
            return null;
        }

        Note::deleteNote(Note::getNote($target['guid']));

        $io->success("Successfully deleted");

        return null;
    }
}
