# .\run.ps1 install
function InstallDependencies {
    Write-Host "Installing Composer dependencies..."
    composer install
    Write-Host "Create DB..."
    php bin/console doctrine:database:create
    Write-Host "Make migration..."
    php bin/console make:migration -n
    Write-Host "Migrate..."
    php bin/console doctrine:migrations:migrate -n
    Write-Host "Fixtures Load..."
    php bin/console doctrine:fixtures:load -n
}

# .\run.ps1 run
function RunDevelopment {
    Write-Host "Starting Symfony server..."
    symfony server:start --no-tls --port=3000
}

$command = $args[0]

switch ($command) {
    "install" { InstallDependencies }
    "run" { RunDevelopment }
    default { Write-Host "Invalid command. Supported commands: install, run" }
}
