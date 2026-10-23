<?php

namespace Tests\Unit;

use Illuminate\Support\Env;
use Tests\TestCase;

class DatabaseConfigTest extends TestCase
{
    public function test_mysql_config_falls_back_to_wasmer_environment_variables(): void
    {
        $contents = file_get_contents(base_path('config/database.php'));

        $this->assertStringContainsString(
            "env('DB_DATABASE', env('DB_NAME', 'laravel'))",
            $contents
        );
        $this->assertStringContainsString(
            "env('DB_USERNAME', env('DB_USER', 'root'))",
            $contents
        );
    }

    public function test_mysql_username_falls_back_to_db_user(): void
    {
        $repository = Env::getRepository();
        $originalUsername = $repository->get('DB_USERNAME');
        $originalUser = $repository->get('DB_USER');

        try {
            $repository->clear('DB_USERNAME');
            $repository->set('DB_USER', 'wasmer_user');

            $config = require dirname(__DIR__, 2).'/config/database.php';

            $this->assertSame('wasmer_user', $config['connections']['mysql']['username']);
        } finally {
            if ($originalUsername === null) {
                $repository->clear('DB_USERNAME');
            } else {
                $repository->set('DB_USERNAME', $originalUsername);
            }

            if ($originalUser === null) {
                $repository->clear('DB_USER');
            } else {
                $repository->set('DB_USER', $originalUser);
            }
        }
    }
}
