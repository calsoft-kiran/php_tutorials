<?php

namespace App\Command\App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'App\Command\CreateUserCommand:New',
    description: 'Create New User',
)]
class CreateUserCommandNewCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
        ->addArgument('email', InputArgument::OPTIONAL, 'Enter User Email')
        ->addArgument('username', InputArgument::OPTIONAL, 'Enter User Username')
        ->addArgument('mobilenumber', InputArgument::OPTIONAL, 'Enter User MobileNumber');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $username = $input->getArgument('username');
        if (!$username) {
            $question = new Question('Please enter username:(default: abc): ', 'abc');
            $question->setValidator(function ($answer) {
                if (empty($answer)) {
                    throw new \RuntimeException('Username cannot be empty.');
                }
                if (!preg_match('/^[a-zA-Z]{3,}$/', $answer)) {
                    throw new \RuntimeException('Invalid username! Use at least 3 letters, no numbers or symbols.');
                }
                return $answer;
            });
            $question->setMaxAttempts(3);
            $username = $helper->ask($input, $output, $question);
        }

        $email = $input->getArgument('email');
        if (!$email) {
            $question = new Question('Please enter email: (default: user@example.com): ', 'user@example.com');
            $question->setValidator(function ($answer) {
                if (empty($answer)) {
                    throw new \RuntimeException('email cannot be empty.');
                }
                if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                    throw new \RuntimeException('Invalid email address.');
                }
                return $answer;
            });
            $question->setMaxAttempts(3);
            $email = $helper->ask($input, $output, $question);
        }

        $mobilenumber = $input->getArgument('mobilenumber');
        if (!$mobilenumber) {
            $question = new Question('Please enter mobilenumber: (default: 9876543210): ', '9876543210');
            $question->setValidator(function ($answer) {
                if (!preg_match('/^\d{10}$/', $answer)) {
                    throw new \RuntimeException('Invalid mobile number! It must contain 10 digits only.');
                }
                return $answer;
            });
            $question->setMaxAttempts(3);
            $mobilenumber = $helper->ask($input, $output, $question);
        }
        $output->writeln("✅ Username: <info>$username</info>");
        $output->writeln("✅ Email: <info>$email</info>");
        $output->writeln("✅ Mobile Number: <info>$mobilenumber</info>");
        return Command::SUCCESS;
    }
}
