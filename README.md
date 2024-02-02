# API

## Работа с FactionAPI
### Регистрация игроков:
Метод: ```FactionAPI::registerPlayer()```

Передаваемые переменные: ```$name```, ```$faction_id```, ```$rank_id```

Пример:
```php
FactionAPI::registerPlayer("Игрок", "f1", "r1");
FactionAPI::getPlayer(): CustomPlayer
```

При передаче неправильной фракции или ранга выставляется пустой ранг и класс

Метод возвращает класс игрока ```CustomPlayer```
### Получение Класса игрока
Метод: ```FactionAPI::getPlayer()```

Передаваемые переменные: ```$name```

Пример:
```php
FactionAPI::getPlayer("Игрок");
```

Метод возвращает класс игрока ```CustomPlayer```

При передаче неправильного имени игрока вернет ошибку

## Работа с классом CustomPlayer

```php
$player = FactionAPI::getPlayer("Игрок");
```
### Чтение данных:
- Получить имя игрока:
```php
$player->getName();
```
- Получить класс Fraction от CustomPlayer:
```php
$player->getFaction();
```
- Получить класс Rank от CustomPlayer:
```php
$player->getRank();
```
### Запись данных
- Назначить Фракцию:
```php
$player->setFaction($faction_id);
```
- Назначить Ранк:
```php
$player->setRank($rank_id);
```
### Сохранение данных
```php
$player->setFaction($faction_id);
$player->setRank($rank_id);
$player->savePlayer();

```


## Работа с классом Faction
Пример получения CustomPlayer от FactionAPI и выбор объекта Faction
```php
$player = FactionAPI::getPlayer("Игрок");
$faction = $player->getFaction();
```
### Чтение данных
Ид Фракции:
```php
$faction->getId();
```
Имя фракции:
```php
$faction->getName();
```
Тип фракции:
```php
$faction->getType();
```
Выбор ранка по идентификатору из текущей фракции:
```php
$faction->getRank($rank_id);
```
Проверка наличия ранка во фракции:
```php
$faction->checkRank($rank_id);
```
Получение списка ранков из текущей фракции. Доступно несколько вариантов получения (string/array)
```Faction::getRankList() : string|array```
```php
$type = \XackiGiFF\FactionPackAPI\factions\faction\Faction::TYPE_STRING;
```
Или
```php
$type = \XackiGiFF\FactionPackAPI\factions\faction\Faction::TYPE_ARRAY;
```
Непосредственно получение:
```php
$faction->getRankList(Faction::TYPE_ARRAY);
```

## Работа с классом Rank
Пример получения CustomPlayer от FactionAPI и выбор объекта Faction
```php
$player = FactionAPI::getPlayer("Игрок");
$faction = $player->getRank();
```
### Чтение данных
```php
Rank::getId() : string|int // Ид ранка
Rank::getName() : string // Название ранка
Rank::getPrice() : int // Цена
Rank::getPay() : int // Периодические выплаты
Rank::getTime() : int // Период времени
Rank::isDefault() : bool // Является ли ранком по умолчанию
Rank::canWrite() : bool // Может ли писать новости

```
## Работа с менеджером Manager
```php
Manager::getFaction($faction_id) : Faction // Вернет Faction или пустой Faction-заглушку
Manager::getPlayer($name) : CustomPlayer // Вернет класс CustomPlayer
Manager::getRank(Faction $faction, $rank_id); //Получить Rank, передав в параметрах Faction и $rank_id
Manager::getNullFaction(): Faction // Вернет пустой ранк "без ранка"
Manager::isCorrectRank(Faction $faction, $rank_id) : bool // Вернет bool знаечение, есть ли такой ранк у фракции
Manager::isCorrectFaction($faction_id) : bool // Проверка на существование фракции
Manager::getFactionList($type): array|string // Еще один вывод но уже для списка фракций
```
Отдельно упомяну Manager::addToManager()

По сути своей это 
для сохранения различных данных в массивы.
В массивах выделяются слоты памяти, куда записываются объекты, что позволяет сделать Many-To-Many связи и обращаться к ним.

```php
Manager::addToManager($model, $slot, $object)
```

Типы моделей:
```php
const MODEL_FACTIONS = "factions";
const MODEL_USERS = "users";
```

К примеру, мы сохраняем пользователей CustomPlayer так:
```php
Manager::addToManager(Manager::MODEL_USERS, self::getName(), $this);
```

Чтобы потом обратиться к нужному слоту по имени пользователя и получить соответствующий класс игрока.

Новый тест