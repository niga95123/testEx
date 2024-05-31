###**Разработка API для интернет-магазина на Symfony**

**Для сборки и запуска выполнить команду**

```sh
docker-compose up -d --build
```

**После сборки и установки всех зависимостей последовательно выполнить следующие команды:**

 **1. Подключиться к контейнеру**
```sh
docker exec -it shop-php-fpm bash
```
**2. Выполнить миграции** 
```sh
./bin/console doctrine:migrations:migrate
```

###**API**

C документацией можно ознакомится после запуска контейнера по ссылке http://localhost:8888/api/doc