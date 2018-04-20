<?php

namespace Mionote\Command\Note;

use Mionote\Note\Note;
use Evernote\Enml\Converter\EnmlToHtmlConverter;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Mionote\Note\Notebook;
use Mionote\Common\File;

/**
 * The Command for Create note
 *
 * Examples:
 * mionote note:create --title Hello --notebook Job --tags=job,mio --content "Test content"
 * ;; Create a note with title "Hello", notebook "Job", tags "job" and "mio", content is "Test content"
 * ;; IF FILL CONTENT IN COMMAND LINE, mionote WILL NOT CALL YOUR EDITOR
 *
 * Option "notebook" is optional, defaults to "Notes"
 * Option "tags" are seperated by ","
 */
class Create extends BaseCommand
{
    protected function configure()
    {
        $this->setName("note:create");
        $this->setDescription("Create a note")->setHelp("Create a note");

        $this->addOption("title", 'title', InputOption::VALUE_REQUIRED, 'Title', null);
        $this->addOption("content", 'content', InputOption::VALUE_OPTIONAL, 'Content', null);
        $this->addOption("notebook", 'notebook', InputOption::VALUE_OPTIONAL, 'Which notebook the note saved, default Notes', 'Notes');
        $this->addOption("tags", 'tags', InputOption::VALUE_OPTIONAL, "Tags for the note will be created, seperated by \",\"", '');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $_title    = $input->getOption('title');
        $content  = $input->getOption('content');
        $_tags     = $input->getOption('tags');
        $_notebook = $input->getOption('notebook');

        $io = new SymfonyStyle($input, $output);

        $tags = array_unique(array_map('trim', explode(',', $_tags)));

        $tags = array_filter($tags, function($x) { return strlen($x) !== 0; });

        if ($_title === null) {
            $io->caution("The title CAN NOT Be empty");
            return;
        }

        $notebook  = Notebook::getNotebook($_notebook);
        if ($notebook === null) {
            if (Notebook::createNotebook($_notebook) === false ) {
               $io->caution("Failed to create note \"{$_notebook}\" ");

               return;
            }

            $io->note("The Notebook \"${_notebook}\" has been automatically created");
            $notebook  = Notebook::getNotebook($_notebook);
        }

        if ($content == null) {
            $config_path = File::getHome() . '/' . File::CONFIG_PATH;
            $file_name   = md5($_title . time()) . '.md';

            $editor = exec("which emacs");

            if ($editor === '') {
                $io->caution("Please configure editor");

                return;
            }

            $target_file = $config_path . '/' . $file_name;
            exec("emacs {$target_file}");
            $content = file_get_contents($target_file);
            @unlink($target_file);


        }

        $success = Note::createNote($_title, $content, $notebook, $tags);

        if ($success) {
            $io->success("The note was created successfully");

            return null;
        }

        $io->caution("Failed to create the note");
    }
}
