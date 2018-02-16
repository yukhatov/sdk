# config valid only for current version of Capistrano
lock '3.5.0'

#set :application, 'SDK'
set :repo_url, 'git@bitbucket.org:tapgerine/sdk.git'

# To make safe to deplyo to same server
set :tmp_dir, "/tmp"

# Default branch is :master
# ask :branch, `git rev-parse --abbrev-ref HEAD`.chomp

# Default deploy_to directory is /var/www/my_app_name
#set :deploy_to, '/home/sdk'

# Default value for :scm is :git
# set :scm, :git

# Default value for :format is :airbrussh.
# set :format, :airbrussh

# You can configure the Airbrussh format using :format_options.
# These are the defaults.
# set :format_options, command_output: true, log_file: 'log/capistrano.log', color: :auto, truncate: :auto

# Default value for :pty is false
# set :pty, true

# Default value for :linked_files is []
set :linked_files, fetch(:linked_files, []).push('app/config/parameters.yml')

# Default value for linked_dirs is []
set :linked_dirs, fetch(:linked_dirs, []).push('var')

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for keep_releases is 5
# set :keep_releases, 5

#after 'deploy:starting', 'composer:install_executable'
after 'deploy:starting', 'symfony:cache:clear'
#after 'deploy:updated', 'symfony:assets:install'
#after 'deploy:updated', 'deploy:migrate'

namespace :deploy do
    desc 'composer update'
    task :composer_update do
        on roles(:web) do
            within release_path do
                execute 'composer', 'update'
            end
        end
    end

    after :updated, 'deploy:composer_update'
end