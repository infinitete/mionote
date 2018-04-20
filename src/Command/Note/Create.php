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
        $this->setDescription("创建笔记")->setHelp("创建一条笔记");

        $this->addOption("title", 'title', InputOption::VALUE_REQUIRED, '标题', null);
        $this->addOption("content", 'content', InputOption::VALUE_OPTIONAL, '内容', null);
        $this->addOption("notebook", 'notebook', InputOption::VALUE_OPTIONAL, '(保存到)哪个笔记本', 'Notes');
        $this->addOption("tags", 'tags', InputOption::VALUE_OPTIONAL, "笔记标签，多个标签使用\",\"分隔", '');
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
            $io->caution("标题不能为空");
            return;
        }

        $notebook  = Notebook::getNotebook($_notebook);
        if ($notebook === null) {
            if (Notebook::createNotebook($_notebook) === false ) {
               $io->caution("Notebook {$_notebook} 创建失败");

               return;
            }

            $io->note("Notebook ${_notebook} 已经被创建");
            $notebook  = Notebook::getNotebook($_notebook);
        }

        if ($content == null) {
            $config_path = File::getHome() . '/' . File::CONFIG_PATH;
            $file_name   = md5($_title . time()) . '.md';

            $editor = exec("which emacs");

            if ($editor === '') {
                $io->caution("请配置好编辑器");

                return;
            }

            $target_file = $config_path . '/' . $file_name;
            exec("emacs {$target_file}");
            $content = file_get_contents($target_file);
            @unlink($target_file);


        }

        $success = Note::createNote($_title, $content, $notebook, $tags);

        if ($success) {
            $io->success("笔记创建成功");

            return null;
        }

        $io->caution("笔记创建失败");
    }
}
