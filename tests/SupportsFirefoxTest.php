<?php

namespace Laravel\Dusk\Tests;

use Laravel\Dusk\Firefox\SupportsFirefox;
use Laravel\Dusk\DuskServiceProvider;
use Orchestra\Testbench\TestCase;

class SupportsFirefoxTest extends TestCase
{
    use SupportsFirefox;

    protected function setUp(): void
    {
        parent::setUp();

        // FirefoxProcessTest already downloads binaries
        // $this->artisan('dusk:firefox-driver');
    }

    public function getPackageProviders($app)
    {
        return [DuskServiceProvider::class];
    }

    public function test_it_can_run_firefox_process()
    {
        $process = static::buildFirefoxProcess(['-v']);

        $process->start();

        // Wait for the process to start up, and output any issues
        sleep(2);

        $process->stop();

        $this->assertStringContainsString('geckodriver', $process->getOutput());
        $this->assertSame('', $process->getErrorOutput());
    }
}
