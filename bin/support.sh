#!/usr/bin/env bash

# Скрипт поддержки, в котором описаны методы необходимые для работы

# Метод определяет, запущен он в контейнере или нет
# 0 - запущен в корневой системе, 1 - запущен в docker контейнере
# @return int
function in_container_inside()
{
    local RESULT=0
    if [ -f "/proc/1/cgroup" ]
    then
        RESULT="$(cat /proc/1/cgroup | grep docker | wc -w)"
    fi
	if [ $RESULT -gt 0 ]
    then
        RESULT=1
    fi
    echo "$RESULT"
    return "$RESULT"
}

# Метод выводит базовое имя контейнеров
function get_base_name()
{
    local unamestr=`uname`
    local rp=''
    if [ "$unamestr" == 'Linux' ]; then
       rp="realpath"
    elif [ "$unamestr" == 'Darwin' ]; then
       rp="grealpath"
    else
        echo "Для данной системы не определена программа, получения абсолютного пути."
        return 1;
    fi

    echo $(basename "$($rp $(pwd))" | awk '{print tolower($0)}' | sed -e 's/\W//g')
}

# Метод выводит базовый путь проекта
function get_base_path()
{
    echo $(pwd);
}

# Метод выполняет команду либо в консоли (если внутри контейнера), либо через docker exec
# @param $1 Комманда, которую нам нужно выполнить
function run()
{
    local DOCKER_INSIDE=$(in_container_inside)
    local RUNNING_CMD=${1:-''}
    local COMPOSE_FILES=$(get-compose-file-string)
    local BASENAME=$(get_base_name)
    local RESULT=0

    if [ "$(echo $RUNNING_CMD | wc -w)" -eq 0 ]
    then
        echo "Необходимо указать команду, которую нужно выполнить"
    fi

    if [ "$DOCKER_INSIDE" -eq 1 ]
    then
        bash -c "$RUNNING_CMD"
        RESULT="$?"
    else
        if [ $(echo "$BASENAME" | wc -w) -eq 0 ]
        then
            echo "Не могу выполнить команду, не указано базовое имя контейнера"
            return 1
        else
            eval 'docker-compose $COMPOSE_FILES exec -T fpm bash -c "$RUNNING_CMD"'
            RESULT="$?"
        fi
    fi
    return "$RESULT"
}

# Метод проверяет, запущен ли ssh-agent и запускает его, если нужно
function prepare_ssh_agent()
{
    echo "Not yet"
}

# Метод обновления зависимостей
function composer_update()
{
    run "composer install --no-interaction --no-progress;"
}

# Start ssh-agent section

# Имя файла-сокета агента, для текущего проекта. Выводится в stdout.
# return 0
function ssh-agent-socket-name()
{
    echo "/tmp/container.partner.sock";
}

# Определяем статус агента
# return 0 - если агент запущен
#        1 - если агент не запущен
function ssh-agent-detect()
{
    ps -ax | grep "[s]sh-agent -a $(ssh-agent-socket-name)" >> /dev/null
    return "$?"
}

# Получение pid агента. Выводится в stdout.
# return 0 - если агент запущен
#        1 - если агент не запущен
function ssh-agent-pid()
{
    local RESULT=1
    ssh-agent-detect
    if [ "$?" -eq 0 ]
    then
        ps -ax | grep "[s]sh-agent -a $(ssh-agent-socket-name)" | cut -f 2 -d " "
        RESULT=0
    fi
    return "$RESULT"
}

# Запуск агента
# return 0 - если агент был запущен или его запуск не нужен
#        > 0 - если агент небыл запущен
function ssh-agent-start()
{
    local RESULT=0
    ssh-agent-detect
    if [ "$?" -eq 1 ]
    then
        ssh-agent -a $(ssh-agent-socket-name) >> /dev/null
        RESULT="$?"
        if [ "$RESULT" -eq 0 ]
        then
            SSH_AUTH_SOCK=$(ssh-agent-socket-name) ssh-add >> /dev/null
        fi
    fi
    return "$RESULT"
}

# Остановка агента
# return 0 - если агент был остановлен
#        > 0 - если агент не был остановлен
function ssh-agent-stop()
{
    local PID=$(ssh-agent-pid)
    if [ "$?" -eq 0 ]
    then
        kill -n 15 "$PID"
        local RESULT="$?"
    fi;
    return "$RESULT"
}
# End ssh-agent section

# Метод получения строки, содержащей подключаемые docker-compose файлы
function get-compose-file-string()
{
    local BASEPATH=$(get_base_path);

    echo "-f docker-compose.yml";
}

function lint()
{
    local PRELINT="$3"
    local EXEC="$2"
    local CHANGED=''
    for i in `git diff --cached --diff-filter=ACMR --name-only | cat`; do
        if [[ $i =~ $1 ]]
        then
            CHANGED="$CHANGED\"$i\" "
        fi
    done
    if [[ $CHANGED == '' ]]
    then
        echo "$1 not touched"
        EXITCODE=0
        return
    fi
    if [[ $PRELINT ]]
    then
        run "cd /var/www && $PRELINT $CHANGED"
    fi

    local CMD=$(run "cd /var/www/ && $EXEC $CHANGED; echo [Errorcode: \$?]")
    echo "$CMD | tr '\r' '\n' | sed 's/\/var\/www\///g'"
    EXITCODE=$(echo $CMD | tr '\r' '\n;' | grep 'Errorcode:' | sed 's/.*: //g' | sed 's/\]//g')
}

function get_diff_files_by_ext()
{
    local EXTENSION=$1
    local GIT_ARGS=''
    local CHANGED=''
    if [[ $2 != "" ]];
    then
        GIT_ARGS=$2
    fi
    for i in $(git diff --cached --name-only | cat); do
        if [[ $i =~ $EXTENSION ]]
        then
            CHANGED="$CHANGED\"$i\" "
        fi
    done
    if [[ $CHANGED != '' ]];
    then
        echo $CHANGED
    else
        return 1
    fi
}

function init_hooks()
{
    local HOOKS=( 'pre-commit' );
    local BASE_PATH="$(pwd)/hooks";

    if [ ${#HOOKS[@]} -gt 0 ]; then
        for HOOK in "${HOOKS[@]}"; do
            local DST_HOOK="$(pwd)/.git/hooks/$HOOK"
            if [ -f $DST_HOOK ]; then
                rm -rf $DST_HOOK;
            fi;
            if [ ! -s $DST_HOOK ]; then
                ln -s $BASE_PATH/$HOOK $DST_HOOK
            fi;
        done;
    fi;
}