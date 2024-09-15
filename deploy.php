<?php

namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'https://github.com/AntonLeontev/greensend.git');
set('keep_releases', 2);

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', [
    'storage',
]);

task('build', function () {
    cd('{{release_path}}');
    run('npm install');
    run('npm run build');
});

// Hosts

host('94.241.174.226')
    ->set('remote_user', 'deployer')
    ->set('deploy_path', '~/greensend');

// Hooks

after('deploy:failed', 'deploy:unlock');
after('deploy:vendors', 'build');
