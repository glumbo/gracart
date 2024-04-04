<?php

namespace Glumbo\Gracart\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Throwable;

class Infomation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sc:info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get infomation S-Cart';
    const LIMIT = 10;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info(config('grakan.name').' - '.config('grakan.title'));
        $this->info(config('grakan.auth').' <'.config('grakan.email').'>');
        $this->info('Front version: '.config('grakan.version'));
        $this->info('Front sub-version: '.config('grakan.sub-version'));
        $this->info('Core: '.config('grakan.core'));
        $this->info('Core sub-version: '.config('grakan.core-sub-version'));
        $this->info('Type: '.config('grakan.type'));
        $this->info('Homepage: '.config('grakan.homepage'));
        $this->info('Github: '.config('grakan.github'));
        $this->info('Facebook: '.config('grakan.facebook'));
        $this->info('API: '.config('grakan.api_link'));
    }
}
