default_run_options[:pty] = true
ssh_options[:forward_agent] = false

set :deploy_to,     "/data/www/satellite"
set :user,          "work"

set :use_sudo, false

set :git_enable_submodules, 1

namespace :deploy do
    task :restart, :roles => :app, :except => { :no_release => true } do
        run "chmod -R 777 #{release_path}/app/cache/"
        run "supervisorctl -c /etc/supervisord/supervisord.conf restart php-fpm"
        run "mv #{release_path}/.git #{release_path}/git-bak"
    end
end