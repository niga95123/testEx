###**Тестовое задание**

**Для сборки и запуска выполнить команду**

```sh
docker-compose up -d --build
```

**После сборки и установки всех зависимостей последовательно выполнить следующие команды:**

 **1. Выполнить миграции**
```sh
docker-compose exec -it shop-php-fpm symfony console doctrine:migrations:migrate
```
**2. Загрузить фикстуры** 
```sh
docker-compose exec -it shop-php-fpm symfony console doctrine:fixtures:load --append
```

###**API**

C документацией можно ознакомится после запуска контейнера по ссылке http://localhost:8888/api/doc

Так же учел добавление в заголовках X-Debug-Time и X-Debug-Memory