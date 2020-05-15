<?php

namespace Laravel\Dusk\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dusk:install
                {--firefox : Generate DuskTestCase class for Mozilla Firefox instead of Google Chrome.}
                {--proxy= : The proxy to download the binary through (example: "tcp://127.0.0.1:9000")}
                {--ssl-no-verify : Bypass SSL certificate verification when installing through a proxy}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Dusk into the application';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! is_dir(base_path('tests/Browser/Pages'))) {
            mkdir(base_path('tests/Browser/Pages'), 0755, true);
        }

        if (! is_dir(base_path('tests/Browser/Components'))) {
            mkdir(base_path('tests/Browser/Components'), 0755, true);
        }

        if (! is_dir(base_path('tests/Browser/screenshots'))) {
            $this->createScreenshotsDirectory();
        }

        if (! is_dir(base_path('tests/Browser/console'))) {
            $this->createConsoleDirectory();
        }

        foreach ($this->stubPaths() as $stub => $file) {
            if (! is_file($file)) {
                copy(__DIR__.'/../../stubs/'.$stub, $file);
            }
        }

        $this->info('Dusk scaffolding installed successfully.');

        if ($this->option('firefox')) {
            $this->comment('Downloading Geckodriver binaries...');

            $this->call('dusk:firefox-driver', $this->driverCommandArgs());
        } else {
            $this->comment('Downloading ChromeDriver binaries...');

            $this->call('dusk:chrome-driver', $this->driverCommandArgs());
        }
    }

    /**
     * Create the screenshots directory.
     *
     * @return void
     */
    protected function createScreenshotsDirectory()
    {
        mkdir(base_path('tests/Browser/screenshots'), 0755, true);

        file_put_contents(base_path('tests/Browser/screenshots/.gitignore'), '*
!.gitignore
');
    }

    /**
     * Create the console directory.
     *
     * @return void
     */
    protected function createConsoleDirectory()
    {
        mkdir(base_path('tests/Browser/console'), 0755, true);

        file_put_contents(base_path('tests/Browser/console/.gitignore'), '*
!.gitignore
');
    }

    /**
     * Find the class stubs to copy into the application.
     *
     * @return array
     */
    protected function stubPaths()
    {
        $stubs = [
            'ExampleTest.stub' => base_path('tests/Browser/ExampleTest.php'),
            'HomePage.stub' => base_path('tests/Browser/Pages/HomePage.php'),
            'Page.stub' => base_path('tests/Browser/Pages/Page.php'),
        ];

        if ($this->option('firefox')) {
            $stubs['FirefoxDuskTestCase.stub'] = base_path('tests/DuskTestCase.php');
        } else {
            $stubs['DuskTestCase.stub'] = base_path('tests/DuskTestCase.php');
        }

        return $stubs;
    }

    /**
     * Build arguments for the driver download command.
     *
     * @return array
     */
    protected function driverCommandArgs()
    {
        $args = ['--all' => true];

        if ($this->option('proxy')) {
            $args['--proxy'] = $this->option('proxy');
        }

        if ($this->option('ssl-no-verify')) {
            $args['--ssl-no-verify'] = true;
        }

        return args;
    }
}
