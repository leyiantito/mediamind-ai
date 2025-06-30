<?php

namespace MediaMindAI\Providers;

use PDO;
use PDOException;
use MediaMindAI\Core\Database\Model;
use MediaMindAI\Core\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('db', function ($app) {
            $config = $app->get('config')->get('database');
            
            $dsn = sprintf(
                '%s:host=%s;dbname=%s;charset=%s',
                $config['connection'],
                $config['host'],
                $config['database'],
                $config['charset']
            );
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            try {
                $pdo = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $options
                );
                
                // Set the connection on the Model
                Model::setConnection($pdo);
                
                return $pdo;
            } catch (PDOException $e) {
                throw new \RuntimeException("Database connection failed: " . $e->getMessage());
            }
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Boot any database services
    }
}
