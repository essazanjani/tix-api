<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeDTOCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:dto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new DTO class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'DTO';

    /**
     * Get the stubs file for the generator.
     */
    protected function getStub(): string
    {
        return base_path('stubs/dto.stub');
    }

    /**
     * Get the default namespace for the class.
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '/DTOs';
    }
}
