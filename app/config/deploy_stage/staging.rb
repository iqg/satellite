server '10.0.0.10', :app, :web, :primary => true

set :branch,      "staging"
set :user,        "work"

set :deploy_to,   "/var/www/satellite.iqianggou.lab"

set :git_enable_submodules, 1

namespace :deploy do
    task :restart, :roles => :app, :except => { :no_release => true } do
        #run "mv #{release_path}/.git #{release_path}/git-bak"
        run "SYMFONY_ENV=prod #{release_path}/app/console doctrine:cache:clear-metadata"
    end
end