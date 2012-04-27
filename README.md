# Создание нового проекта.
Для облегчения создания новых проектов, в системе репозиториев git.armd.ru загружен sandbox, содержащий в себе все необходимые конфигурации и зависимости CMS.

Все операции даны для ОС систем семейства linux, для windows команды могут отличаться.

Для того, что бы создать новый проект на основе CMS необходимо выполнить несколько действий:

1. Зайти на http://git.armd.ru/cmssandbox и убедится, что есть доступ к этому проекту;

2. Клонировать репозитарий в локальную директорию проекта;

    git clone git@git.armd.ru:cmssandbox.git /www/cmssandbox.local
    cd /www/cmssandbox.local

3. Скопировать конфигурационный файл CMS и поправить его, указав реальные данные БД (БД должна быть в кодировке utf8), локали и уникального ключа;

    cp app/config/parameters.yml-dist app/config/parameters.yml
    
4. Установить composer;

    curl -s http://getcomposer.org/installer | php -- 
    
5. Установить используемые CMS модули (бандлы) CMS. Для работы этой команды пользователь из под которого выполняется команда должен иметь доступ ко всем зависимым репозиториям на git.armd.ru;

    php composer.phar install

6. Обновить схему баз данных;

    php app/console doctrine:schema:update --force
       
7. Опционально можно загрузить тестовую структуру страниц и данных (предварительно очищает бд, подробнее см. app/console help);

    php app/console doctrine:fixtures:load
    
8. Добавить администратора CMS;

    php app/console fos:user:create root --super-admin
    

Структура развернутого инстанса:

    ├── app # настройки текущего инстанса
    ├── src # модули (бандлы) для данного инстанса
    ├── vendor #сторонние библиотки
    │   ├── .composer
    │   ├── armd # стандартные модули CMS
    │   ├── ... # другие модули Symfony2     
    └── web # document-root веб сервера
        └── bundles # symlink на внешние ресурсы модулей (css, images, js)

# После установки sandbox

1. Удаляем диреторию .git;
2. Заводим новый проект на git.armd.ru (например Example);
3. Инициализируем репозиторий в рабочей копии;
4. Добавляем git.armd.ru в рабочую копию "git remote add origin git@git.armd.ru:example.git";
5. Заливаем проект в общий репозиторий "git push origin master";