<?php

// Create necessary directories
$directories = [
    'bootstrap',
    'app/Http/Controllers',
    'app/Models',
    'app/Core/Console',
    'routes',
    'public',
    'database/migrations'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "Created directory: $dir\n";
    }
}

// 1. Create/Update composer.json
file_put_contents('composer.json', '{
    "name": "mediamindai/application",
    "description": "MediaMind AI Application",
    "type": "project",
    "require": {
        "php": "^8.0",
        "illuminate/database": "^8.0",
        "illuminate/routing": "^8.0",
        "illuminate/events": "^8.0",
        "vlucas/phpdotenv": "^5.3",
        "symfony/var-dumper": "^5.4",
        "symfony/console": "^5.4"
    },
    "autoload": {
        "psr-4": {
            "MediaMindAI\\\\": "app/"
        }
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}');
echo "Updated composer.json\n";

// 2. Create bootstrap/app.php
file_put_contents('bootstrap/app.php', '<?php

$app = new MediaMindAI\Core\Application(
    $_ENV["APP_BASE_PATH"] ?? dirname(__DIR__)
);

return $app;');
echo "Created bootstrap/app.php\n";

// 3. Create artisan file
file_put_contents('artisan', '#!/usr/bin/env php
<?php

define("LARAVEL_START", microtime(true));

require __DIR__."/vendor/autoload.php";

$app = require_once __DIR__."/bootstrap/app.php";

$kernel = $app->make(MediaMindAI\Core\Console\Kernel::class);

$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput,
    new Symfony\Component\Console\Output\ConsoleOutput
);

$kernel->terminate($input, $status);

exit($status);');
echo "Created artisan\n";

// 4. Create Console Kernel
file_put_contents('app/Core/Console/Kernel.php', '<?php

namespace MediaMindAI\Core\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [];

    protected function schedule(Schedule $schedule)
    {
        // $schedule->command("inspire")->hourly();
    }

    protected function commands()
    {
        $this->load(__DIR__."/Commands");
        require base_path("routes/console.php");
    }
}');
echo "Created app/Core/Console/Kernel.php\n";

// 5. Create routes/web.php
file_put_contents('routes/web.php', '<?php

use MediaMindAI\Core\Routing\Router;
use MediaMindAI\Http\Controllers\TestController;

$router = app("router");

$router->get("/", function () {
    return "Welcome to MediaMind AI! Homepage is working!";
});

$router->get("/test", function () {
    return "Test route is working!";
});

$router->get("/controller", [TestController::class, "index"]);');
echo "Created routes/web.php\n";

// 6. Create TestController
file_put_contents('app/Http/Controllers/TestController.php', '<?php

namespace MediaMindAI\Http\Controllers;

class TestController
{
    public function index()
    {
        return "Test Controller is working!";
    }
}');
echo "Created app/Http/Controllers/TestController.php\n";

// 7. Create Test model
file_put_contents('app/Models/Test.php', '<?php

namespace MediaMindAI\Models;

use MediaMindAI\Core\Database\Model;

class Test extends Model
{
    protected $table = "test_table";
    protected $fillable = ["name"];
}');
echo "Created app/Models/Test.php\n";

// 8. Create test migration
file_put_contents('database/migrations/2025_06_29_000000_create_test_table.php', '<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

class CreateTestTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable("test_table")) {
            Schema::create("test_table", function (Blueprint $table) {
                $table->id();
                $table->string("name");
                $table->timestamps();
            });
            echo "Test table created successfully!\\n";
        }
    }

    public function down()
    {
        Schema::dropIfExists("test_table");
    }
}');
echo "Created database migration\n";

// 9. Create public/index.php
file_put_contents('public/index.php', '<?php

require __DIR__."/../vendor/autoload.php";

$app = require_once __DIR__."/../bootstrap/app.php";

$kernel = $app->make(MediaMindAI\\Core\\Http\\Kernel::class);

$response = $kernel->handle(
    $request = MediaMindAI\\Core\\Http\\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);');
echo "Created public/index.php\n";

// 10. Create .htaccess files
file_put_contents('public/.htaccess', '<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>');
echo "Created public/.htaccess\n";

file_put_contents('.htaccess', '<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)/$ /$1 [L,R=301]

    # Handle Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
</IfModule>

# Disable directory browsing
Options -Indexes

# Block access to sensitive files
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

<FilesMatch "^(composer\.json|composer\.lock|package\.json|webpack\.mix\.js|webpack\.mix\.json|yarn\.lock|package-lock\.json|\.gitignore|\.gitattributes|\.env|\.env\.example|\.gitkeep|\.htaccess|\.htpasswd|storage|bootstrap/cache|artisan|server\.php|gulpfile\.js|webpack\.config\.js|phpunit\.xml|phpcs\.xml|phpunit\.xml\.dist|phpmd\.xml|phpstan\.neon|phpcs\.xml\.dist|phpunit\.xml|phpunit\.php|phpcs\.xml|phpunit\.php|phpunit\.php|phpunit\.php|phpunit\.php|phpunit\.php)$">
    Order allow,deny
    Deny from all
</FilesMatch>');
echo "Created .htaccess\n";

// 11. Create README.md
file_put_contents('README.md', '# MediaMind AI

Welcome to MediaMind AI application.

## Requirements

- PHP 8.0 or higher
- Composer
- MySQL 5.7+ or MariaDB 10.2+
- Node.js & NPM (for frontend assets)

## Installation

1. Clone the repository
2. Run `composer install`
3. Copy [.env.example](cci:7://file:///c:/xampp/htdocs/media%20AI/.env.example:0:0-0:0) to [.env](cci:7://file:///c:/xampp/htdocs/media%20AI/.env:0:0-0:0) and update the values
4. Run `php artisan key:generate`
5. Configure your web server to point to the [public](cci:1://file:///c:/xampp/htdocs/media%20AI/app/Core/Application.php:136:4-145:5) directory
6. Run migrations: `php artisan migrate`

## Development

Start the development server:

```bash
php -S localhost:8000 -t public