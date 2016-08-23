server '10.0.0.10', :app, :web, :primary => true

set :branch,	"dev"
set :user,      "work"

set :deploy_to,   "/var/www/satellite.iqianggou.lab"
set :clear_controllers,     false

namespace :deploy do
    task :restart, :roles => :app, :except => { :no_release => true } do
#        run "#{sudo} ~/.dev.clean.cache.sh"
        run "#{release_path}/app/console doctrine:cache:clear-metadata"
    end
end
