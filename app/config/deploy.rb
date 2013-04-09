set :application, "Portal - culture.ru"

set :domain,      "web01.culture.ru"
set :user,        "shpeyzulaev"
set :deploy_to,   "/www/deploy.culture.ru"
set :app_path,    "app"
set :web_path,    "web"


set :repository,  "git@git.armd.ru:mk_dev_armd_ru.git"
set :scm,         :git
set :branch,      "master"
set :deploy_via, :remote_cache

set :model_manager, "doctrine"

role :web,        domain                       # Your HTTP server, Apache/etc
role :app,        domain, :primary => true       # This may be the same as your `Web` server
role :db,         domain, :primary => true       # This is where Symfony2 migrations will run

#ssh_options[:forward_agent] = true
default_run_options[:pty] = true

set :shared_files, ["app/config/parameters.yml"]
set :shared_children, [app_path + "/logs", web_path + "/uploads", "vendor"]
set :use_composer, true
set :composer_options, '-n' # --prefer-dist
set :vendors_mode, "install"
set :assets_symlinks, true
set :cache_warmup, false
set :dump_assetic_assets, true
set :use_sudo, false
set :interactive_mode, false
set :php_bin, "php -d memory_limit=650M"

set :group_writable, false

set  :keep_releases, 3

# disable unsuited (for ms, ms_en env) asks
set :clear_controllers, false
set :cache_warmup, false

# Be more verbose by uncommenting the following line
#logger.level = Logger::MAX_LEVEL


namespace :custom_culture do
  task :default do
    set :symfony_env_prod, "dev"
    symfony.doctrine.migrations.migrate

    set :symfony_env_prod, "dev_en"
    symfony.doctrine.migrations.migrate

    set :symfony_env_prod, "ms"
    symfony.cache.clear

    set :symfony_env_prod, "ms_en"
    symfony.cache.clear

  end
end

after "deploy:restart", "custom_culture"
after "deploy", "deploy:cleanup"
