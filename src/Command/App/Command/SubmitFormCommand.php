<?php

namespace App\Command\App\Command;

use App\Form\AddUserFormType;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Console\Question\Question;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Controller\AircraftDetailController;

#[AsCommand(
    name: 'App\Command\SubmitFormCommand',
    description: 'Submit add user form',
)]
class SubmitFormCommand extends Command
{
    private FormFactoryInterface $formFactory;
    private ValidatorInterface $validator;
    private $logger;
    private $aircraftDetailController;
    private $requestStack;

    public function __construct(AircraftDetailController $aircraftDetailController, RequestStack $requestStack, FormFactoryInterface $formFactory, ValidatorInterface $validator,LoggerInterface $customLogger)
    {
        parent::__construct();
        $this->formFactory = $formFactory;
        $this->validator = $validator;
        $this->logger  = $customLogger;
        $this->aircraftDetailController=$aircraftDetailController;
        $this->requestStack = $requestStack;

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $this->logger->info('This is an INFO log');
     

        // Get user input
        $usernameQuestion = new Question('Enter username: ');
        $emailQuestion = new Question('Enter email: ');
        $mobileQuestion = new Question('Enter mobile number: ');

        $username = $helper->ask($input, $output, $usernameQuestion);
        $email = $helper->ask($input, $output, $emailQuestion);
        $mobile = $helper->ask($input, $output, $mobileQuestion);

        // Create form and submit data
        $form = $this->formFactory->create(AddUserFormType::class);
        $form->submit([
            'username' => $username,
            'email' => $email,
            'mobile' => $mobile,
        ]);

        // Validate the form
        if (!$form->isValid()) {
            foreach ($form->getErrors(true) as $error) {
                $output->writeln('<error>' . $error->getMessage() . '</error>');
            }
            return Command::FAILURE;
        }

        // Create a fake request object
        $request = new Request([], [
            'username' => $username,
            'email' => $email,
            'mobile' => $mobile,
        ]);

        // Push fake request into RequestStack to mimic a real HTTP request
        $this->requestStack->push($request);

        // Call the controller action manually
        $this->aircraftDetailController->addNewUser($request);

        // Simulating saving data (Replace this with actual database saving)
        $output->writeln("<info>Form submitted successfully!</info>");
        $output->writeln("✅ Username: $username");
        $output->writeln("✅ Email: $email");
        $output->writeln("✅ Mobile: $mobile");

        return Command::SUCCESS;
    }
}
