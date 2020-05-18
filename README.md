# Создание клана

1. Создание клана (название клана (макс 12 символов без специальных символов и пробелов), описание (макс 30 символов), список участников (для описания участника достаточно полей id и name)) / удаление клана. 
2. Роли для участников клана: 
    * Клан Лидер: может редактировать описание клана, удалять клан, удалять других участников, повышать или понижать в звании других участников клана. Участник, который создал клан - становится клан лидером по умолчанию.  
    * Заместитель: может редактировать описание клана, повышать до заместителя
    * Солдат не имеет никаких привилегий. Любой новых игрок, который приходит в клан, становится солдатом.

3. Добавление новых участников в клан/удаление участников из клана (удалить из клана можно только участника с ролью солдат). 
4. Смена описания клана.
5. Повышение/понижение ролей участников клана. 
6. Получение списка кланов и их участников. 

**Примечание: **

данные приходят извне посредством POST запросов. 
Используем чистый PHP без фреймворков и сторонних сервисов и библиотек. 
Интерфейс или формы для ввода делать не нужно. 
Авторизацию делать не нужно. 

## развернуть проект



```
git clone https://github.com/pavel-lukashevich/base-clan.git
cd base-clan
cp .env.example .env

composer install
```
- создать базу данных, заполнить .env файл
- запустить создание таблиц и пользователей "http://base-clan/run"


## примеры запросов

```

curl --location --request GET 'http://base-clan/seeds/run'

curl --location --request POST 'http://base-clan/clans/index' --form 'session_key=key_2'

curl --location --request POST 'http://base-clan/clans/create' \
--form 'session_key=key_2' \
--form 'title=t5557' \
--form 'description=g5557'

curl --location --request POST 'http://base-clan/clans/show' \
--form 'session_key=key_7'

curl --location --request POST 'http://base-clan/clans/update' \
--form 'session_key=key_6' \
--form 'description=g999'

curl --location --request POST 'http://base-clan/clans/destroy' \
--form 'session_key=key_6'

curl --location --request POST 'http://base-clan/clan-members/join' \
--form 'session_key=key_1' \
--form 'clan_id=2'

curl --location --request POST 'http://base-clan/clan-members/quit' \
--form 'session_key=key_1'

curl --location --request POST 'http://base-clan/clan-members/roleUp' \
--form 'session_key=key_7' \
--form 'user_id=3'

curl --location --request POST 'http://base-clan/clan-members/roleDown' \
--form 'session_key=key_7' \
--form 'user_id=8'
```


# ( ͡° ͜ʖ ͡°)

