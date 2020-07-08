# simple_symfony_api

Пример api для работы со списком книг на symfony

**Что есть:**
1. Получить список книг: GET /api/books/
2. Получить информацию о книге: GET /api/books/id-книги
3. Добавить книгу: POST /api/books, [name => 'имя книги', author => 'автор', 'isRead' => '0/!0/']
4. Изменить информацию о книге: PUT /api/books/id-книги, [name => 'имя книги', author => 'автор', 'isRead' => '0/!0/'] (По идее тут должен быть POST/PATCH)
5. Удалить книгу: DELETE /books/id-книги
6. Получить список книг по автору: POST /author, form-data:['author' => 'имя автора']
7. Регистрация: POST /register, ['username' => 'логин', 'password' => 'пароль', 'email' => 'почта']
8. Аутентификация: POST /api/login_check ['username' => 'логин', 'password' => 'пароль'], в ответе получаем токен, если используете postman - добавлять как "Bearer token"

во всех POST запросах перечисленные поля являются обязательными

подробнее про токен: https://github.com/lexik/LexikJWTAuthenticationBundle

**Чего не хватает:**
1. Моделей (сейчас всё свалено в контроллеры)
