visited 192.168.0.5:8080, got an error - Warning: require(/var/www/html/public/../vendor/autoload.php): Failed to open stream: No such file or directory in /var/www/html/public/index.php on line 13

connected to container, no vendor folder

executed 'composer install'

issues seen
### start ###
Installing dependencies from lock file (including require-dev)
Verifying lock file contents can be installed on current platform.
Your lock file does not contain a compatible set of packages. Please run composer update.

  Problem 1
    - maennchen/zipstream-php is locked to version 3.2.0 and an update of this package was not requested.
    - maennchen/zipstream-php 3.2.0 requires php-64bit ^8.3 -> your php-64bit version (8.2.29) does not satisfy that requirement.
  Problem 2
    - openspout/openspout is locked to version v4.32.0 and an update of this package was not requested.
    - openspout/openspout v4.32.0 requires php ~8.3.0 || ~8.4.0 || ~8.5.0 -> your php version (8.2.29) does not satisfy that requirement.
  Problem 3
    - filament/actions is locked to version v3.3.43 and an update of this package was not requested.
    - filament/actions v3.3.43 requires openspout/openspout ^4.23 -> satisfiable by openspout/openspout[v4.32.0].
    - openspout/openspout v4.32.0 requires php ~8.3.0 || ~8.4.0 || ~8.5.0 -> your php version (8.2.29) does not satisfy that requirement.
### end ###

executed 'composer update'
### start ###
   INFO  Successfully published assets!  

   INFO  Configuration cache cleared successfully.  

   INFO  Route cache cleared successfully.  

   INFO  Compiled views cleared successfully.  

   INFO  Successfully upgraded!  

114 packages you are using are looking for funding.
Use the `composer fund` command to find out more!
### end ###

checked browser, title says no app key, checked .env, not been set

executed 'php artisan key:generate'

page now loads

clicked login button (http://192.168.0.5:8080/index.php/login) -> view [auth.login not found]

clicked register button (http://192.168.0.5:8080/index.php/register) -> view [auth.register] not found

checked resources folder on host
### start ###
2cf149e92708:/var/www/html/resources# tree
.
├── css
│   └── app.css
├── js
│   ├── app.js
│   └── bootstrap.js
└── views
    ├── components
    │   ├── app-layout.blade.php
    │   ├── banner.blade.php
    │   ├── dropdown-link.blade.php
    │   ├── dropdown.blade.php
    │   ├── nav-link.blade.php
    │   ├── responsive-nav-link.blade.php
    │   └── switchable-team.blade.php
    ├── dashboard.blade.php
    ├── layouts
    │   └── app.blade.php
    ├── livewire
    │   └── navigation-menu.blade.php
    ├── modules
    │   └── index.blade.php
    └── welcome.blade.php

7 directories, 15 files
2cf149e92708:/var/www/html/resources# 

### end ###

barryvdh/laravel-debugbar -> the styling is missing, its just a bunch of jumbled text, no styling/layout etc

url does not get tidied up -> http://192.168.0.5:8080/index.php/login becomes http://192.168.0.5:8080/login

docker assets in html folder, suggest having a stadging folder and only copy essential file/folders to html folder

tree results
2cf149e92708:/var/www/html/app# tree
.
├── Actions
│   ├── Fortify
│   │   ├── CreateNewUser.php
│   │   ├── PasswordValidationRules.php
│   │   ├── ResetUserPassword.php
│   │   ├── UpdateUserPassword.php
│   │   └── UpdateUserProfileInformation.php
│   └── Jetstream
│       ├── AddTeamMember.php
│       ├── CreateTeam.php
│       ├── DeleteTeam.php
│       ├── DeleteUser.php
│       ├── InviteTeamMember.php
│       ├── RemoveTeamMember.php
│       └── UpdateTeamName.php
├── Console
│   └── Commands
│       ├── InstallModuleCommand.php
│       └── UninstallModuleCommand.php
├── Filament
│   └── Resources
│       ├── ModuleResource
│       │   └── Pages
│       │       ├── CreateModule.php
│       │       ├── EditModule.php
│       │       └── ListModules.php
│       ├── ModuleResource.php
│       ├── UserResource
│       │   └── Pages
│       │       ├── CreateUser.php
│       │       ├── EditUser.php
│       │       └── ListUsers.php
│       └── UserResource.php
├── Http
│   ├── Controllers
│   │   ├── Controller.php
│   │   ├── DashboardController.php
│   │   └── ModuleController.php
│   └── Middleware
│       └── EnsureTeamContext.php
├── Livewire
│   └── NavigationMenu.php
├── Models
│   ├── Customer.php
│   ├── InstalledModule.php
│   ├── Module.php
│   ├── ModuleDependency.php
│   ├── ModuleSetting.php
│   ├── Team.php
│   └── User.php
├── Providers
│   ├── AppServiceProvider.php
│   ├── FortifyServiceProvider.php
│   └── JetstreamServiceProvider.php
├── Services
│   └── ModuleService.php
└── Traits
    └── HasTeamScope.php

2cf149e92708:/var/www/html# tree config
config
├── fortify.php
├── jetstream.php
├── modules.php
└── permission.php

2cf149e92708:/var/www/html# tree public
public
├── build
│   ├── assets
│   │   ├── app-CnDaDdsp.css
│   │   └── app-DRmlBW5u.js
│   └── manifest.json
├── css
│   └── filament
│       ├── filament
│       │   └── app.css
│       ├── forms
│       │   └── forms.css
│       └── support
│           └── support.css
├── health.php
├── index.php
├── js
│   └── filament
│       ├── filament
│       │   ├── app.js
│       │   └── echo.js
│       ├── forms
│       │   └── components
│       │       ├── color-picker.js
│       │       ├── date-time-picker.js
│       │       ├── file-upload.js
│       │       ├── key-value.js
│       │       ├── markdown-editor.js
│       │       ├── rich-editor.js
│       │       ├── select.js
│       │       ├── tags-input.js
│       │       └── textarea.js
│       ├── notifications
│       │   └── notifications.js
│       ├── support
│       │   └── support.js
│       ├── tables
│       │   └── components
│       │       └── table.js
│       └── widgets
│           └── components
│               ├── chart.js
│               └── stats-overview
│                   └── stat
│                       └── chart.js

2cf149e92708:/var/www/html# tree database
database
├── factories
│   └── CustomerFactory.php
├── migrations
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000001_create_cache_table.php
│   ├── 0001_01_01_000002_create_jobs_table.php
│   ├── 2024_01_01_000003_create_teams_table.php
│   ├── 2024_01_01_000004_create_personal_access_tokens_table.php
│   ├── 2024_01_01_000005_create_permission_tables.php
│   ├── 2024_01_01_000006_create_activity_log_table.php
│   ├── 2024_01_01_000007_create_media_table.php
│   ├── 2024_01_01_000008_create_modules_table.php
│   └── 2025_11_02_085702_create_customers_table.php
├── modules
└── seeders
    ├── DatabaseSeeder.php
    ├── ModuleSeeder.php
    ├── RolePermissionSeeder.php
    └── TestUserSeeder.php

2cf149e92708:/var/www/html# tree routes
routes
├── api.php
├── console.php
└── web.php

2cf149e92708:/var/www/html# tree bootstrap
bootstrap
├── app.php
└── cache
    ├── packages.php
    └── services.php


2cf149e92708:/var/www/html# ls -al
total 728
drwxr-xr-x    1 www-data www-data      4096 Nov  2 19:50 .
drwxr-xr-x    1 root     root          4096 Nov  1 16:30 ..
drwxr-xr-x    2 www-data www-data      4096 Nov  2 19:26 .ai_helper_scripts
-rw-r--r--    1 www-data www-data      1023 Nov  2 19:26 .dockerignore
-rw-r--r--    1 root     root          1741 Nov  2 19:37 .env
-rw-r--r--    1 www-data www-data      1690 Nov  2 19:26 .env.example
drwxr-xr-x    7 www-data www-data      4096 Nov  2 19:54 .git
drwxr-xr-x    4 www-data www-data      4096 Nov  2 19:26 .github
-rw-r--r--    1 www-data www-data       410 Nov  2 19:26 .gitignore
-rw-r--r--    1 www-data www-data     14991 Nov  2 19:26 README.md
drwxr-xr-x   11 www-data www-data      4096 Nov  2 19:26 app
-rw-r--r--    1 www-data www-data       334 Nov  2 19:26 artisan
drwxr-xr-x    1 www-data www-data      4096 Nov  2 19:26 bootstrap
-rw-r--r--    1 www-data www-data      2527 Nov  2 19:26 composer.json
-rw-r--r--    1 www-data www-data    467890 Nov  2 19:35 composer.lock
drwxr-xr-x    2 www-data www-data      4096 Nov  2 19:26 config
drwxr-xr-x    6 www-data www-data      4096 Nov  2 19:26 database
drwxr-xr-x    3 www-data www-data      4096 Nov  2 19:26 docker
-rw-r--r--    1 www-data www-data      8448 Nov  2 19:26 docker-compose.yml
drwxr-xr-x    2 root     root          4096 Nov  2 19:26 modules
drwxr-xr-x  140 root     root          4096 Nov  2 19:26 node_modules
-rw-r--r--    1 root     root        116063 Nov  2 19:26 package-lock.json
-rw-r--r--    1 www-data www-data       460 Nov  2 19:26 package.json
-rw-r--r--    1 www-data www-data        93 Nov  2 19:26 postcss.config.js
drwxr-xr-x    6 www-data www-data      4096 Nov  2 19:26 public
-rw-r--r--    1 www-data www-data      8011 Nov  2 19:26 quick-start.sh
drwxr-xr-x    5 www-data www-data      4096 Nov  2 19:26 resources
drwxr-xr-x    2 www-data www-data      4096 Nov  2 19:26 routes
drwxrwxr-x    5 www-data www-data      4096 Nov  2 19:36 storage
-rw-r--r--    1 www-data www-data      1230 Nov  2 19:26 tailwind.config.js
drwxr-xr-x   67 root     root          4096 Nov  2 19:35 vendor
-rw-r--r--    1 www-data www-data       310 Nov  2 19:26 vite.config.js
