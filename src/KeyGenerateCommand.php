<?php
/**
 * Created by IntelliJ IDEA.
 * User: Max Sky
 * Date: 2019/3/2
 * Time: 15:38
 */

namespace Illuminate\Console;

use Illuminate\Encryption\Encrypter;

class KeyGenerateCommand extends Command {

    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'key:generate';

    protected $signature = 'key:generate
                            {--show : Display the key instead of modifying files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the application key.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {
        $key = $this->generateRandomKey();

        if ($this->option('show')) {
            $this->comment($key);
            exit();
        }

        // Next, we will replace the application key in the environment file so it is
        // automatically setup for this developer. This key gets generated using a
        // secure random byte generator and is later base64 encoded for storage.
        if (!$this->setKeyInEnvironmentFile($key)) {
            return;
        }

        $this->laravel['config']['app.key'] = $key;

        $this->info("Application key [$key] set successfully.");
        exit();
    }

    /**
     * Generate a random key for the application.
     *
     * @return string
     */
    protected function generateRandomKey() {
        return 'base64:' . base64_encode(
                Encrypter::generateKey($this->laravel['config']['app.cipher'])
            );
    }

    /**
     * Set the application key in the environment file.
     *
     * @param  string $key
     *
     * @return bool
     */
    protected function setKeyInEnvironmentFile($key) {
        $currentKey = $this->laravel['config']['app.key'];

        if (strlen($currentKey) !== 0 && (!$this->confirmToProceed())) {
            return false;
        }

        $this->writeNewEnvironmentFileWith($key);

        return true;
    }

    /**
     * Write a new environment file with the given key.
     *
     * @param  string $key
     *
     * @return void
     */
    protected function writeNewEnvironmentFileWith($key) {
        $env_path = base_path('.env');
        file_put_contents($env_path, preg_replace(
            $this->keyReplacementPattern(),
            'APP_KEY=' . $key,
            file_get_contents($env_path)
        ));
    }

    /**
     * Get a regex pattern that will match env APP_KEY with any random key.
     *
     * @return string
     */
    protected function keyReplacementPattern() {
        $escaped = preg_quote('=' . getenv('APP_KEY'), '/');

        return "/^APP_KEY{$escaped}/m";
    }

}