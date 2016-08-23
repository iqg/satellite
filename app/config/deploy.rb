

default_run_options[:pty] = true
ssh_options[:forward_agent] = false


set :application, "satellite 1.0"

set :app_path,    "app"

set :repository,  "git@github.com:iqg/satellite.git"
set :scm,         :git
set :branch,      "master"

set :linked_dirs, ""
set :shared_files, ['app/config/parameters.yml']
set :use_sudo, false
set :file_permissions_paths,  [fetch(:linked_dirs)]


set :composer_bin, "/usr/local/bin/composer"

set :composer_options,  "--no-dev -vvv --prefer-dist --optimize-autoloader"

set  :deploy_via, :remote_cache

set :model_manager, "doctrine"



set  :keep_releases,  10

# Be more verbose by uncommenting the following line
logger.level = Logger::MAX_LEVEL

set :use_composer, true
set :update_vendors, true


set :stages,        %w(aliyun dev staging)
set :default_stage, "staging"
set :stage_dir,     "app/config/deploy_stage"
require 'capistrano/ext/multistage'


namespace :deploy do
    task :restart, :roles => :app, :except => { :no_release => true } do
        run "chmod -R 777 #{release_path}/app/cache/"
        run "#{sudo} service php5-fpm reload"
    end
end
