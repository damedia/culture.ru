set :application, "Portal - culture.ru"

set :domain,      "web01.culture.ru"
set :user,        "shpeyzulaev"
set :deploy_to,   "/www/deploy.culture.ru"
set :app_path,    "app"
set :web_path,    "web"


set :repository,  "git@git.armd.ru:mk_dev_armd_ru.git"
set :scm,         :git
set :branch,      "master"

set :model_manager, "doctrine"

role :web,        domain                         # Your HTTP server, Apache/etc
role :app,        domain                         # This may be the same as your `Web` server
role :db,         domain, :primary => true       # This is where Symfony2 migrations will run

ssh_options[:forward_agent] = true
default_run_options[:pty] = true

set :shared_files, ["app/config/parameters.yml"]
set :shared_children, [app_path + "/logs", web_path + "/uploads", "vendor"]
set :use_composer, true
set :vendors_mode, "install"
set :assets_symlinks, true
set :cache_warmup, false
set :dump_assetic_assets, true

set  :keep_releases,  3

# Be more verbose by uncommenting the following line
logger.level = Logger::MAX_LEVEL