<?php

namespace YllwDigital\YllwAuth\Console;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

class InstallYllwAuth extends GeneratorCommand {
    protected $type = 'Model';
    protected $name = "yllwauth:install";
    protected $description = "Install the yllw auth package.";

    protected $passportTrait = "Laravel\Passport\HasApiTokens";
    protected $yllwAuthenticatable = "YllwDigital\YllwAuth\app\Http\Traits\YllwAuthenticatable";

    public function handle() {
        $this->info("Installing the yllw auth package");
        
        $this->installPassport();
        $this->usePassport();
        $this->addTraits();
    }

    protected function installPassport() {
        $this->call('passport:install');
        $this->call('migrate');
        $this->info("Installed passport.");
        return true;
    }

    protected function usePassport() {
        $path = config_path('auth.php');

        if(file_exists($path)) {
            file_put_contents($path, str_replace("'driver' => 'token'", "'driver' => 'passport'", file_get_contents($path)));
            $this->info("Changed default driver for users to passport.");
            return true;
        }

        $this->error("Auth config file does not exist.");
        return false;
    }

    protected function addTraits() {
        $this->addTrait($this->passportTrait);
        $this->addTrait($this->yllwAuthenticatable);
    }

    protected function addTrait($trait) {
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);
        
        if ($this->alreadyExists($this->getNameInput())) {
            $file = $this->files->get($path);
            $file_array = preg_split('/\n|\r\n?/', $file);

            if (Str::contains($file, [$trait])) {
                $this->info('User model already exists and uses ' . $trait . '.');
                return false;
            }

            $classDefinition = 'class '.$this->getNameInput().' extends';
            foreach ($file_array as $key => $line) {
                if (Str::contains($line, $classDefinition)) {
                    if (Str::endsWith($line, '{')) {
                        $position = $key + 1;
                    } elseif ($file_array[$key + 1] == '{') {
                        $position = $key + 2;
                    }

                    array_splice($file_array, $position, 0, '    use \\'.$trait.';');
                    $this->files->put($path, implode(PHP_EOL, $file_array));

                    $this->info('Model already exists! We just added the ' . $trait . ' trait to it.');
                    return false;
                }
            }

            $this->error('Model already exists! Could not add trait - please add manually.');
            return false;
        }

        $this->makeDirectory($path);
        $this->files->put($path, $this->sortImports($this->buildClass($name)));
        $this->info($this->type.' created successfully.');
    }

    protected function getStub() {
        return __DIR__ . '/stubs/user-model.stub';
    }

    protected function getDefaultNamespace($rootNamespace) {
        return $rootNamespace.'\Models';
    }
}