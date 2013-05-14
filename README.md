FastAuth Library v1.0
=====================

FastAuth - это PHP библиотека позволяющая пользователю не заполняя на сайте учётных
данных зарегистрироваться или войти в систему используя свой профиль с Facebook, Google, PayPal,
Windows Live, ВКонтакте, Mail.ru, Bitly, Foursquare, GitHub, Одноклассников или Yandex.

Для корректной работы библиотеки требуется PHP 5.3 и старше.


Пример использования:
---------------------

    $options = array(
        'client_id' => '<CONSUMER_KEY>',
        'secret' => '<CONSUMER_SECRET>',
        'redirect_uri' => '<REDIRECT_URI>',
    );
    $service = \FastAuth\Service::factory('facebook', $options);
    try {
        $token = $service->retrieveToken($_GET['code']);
        $profile = $service->retrieveProfile($token);
    } catch (\FastAuth\Exception\Exception $e) {
        die('Authentication error: ' . $e->getMessage());
    }
    $uid = $profile->getServiceName() . ':' . $profile->getId();
    echo $uid;

В этом примере показано, как получить профиль пользователя из внешнего источника и
достать из него уникальный идентификатор.
Параметры CONSUMER_KEY, CONSUMER_SECRET и REDIRECT_URI заполняются в
зависимости от настроек приложения созданного на странице
[https://developers.facebook.com/apps](https://developers.facebook.com/apps).

Параметр $_GET['code'], используемый методом retrieveToken, возвращается сервером
Facebook, если пользователь подтвердил согласие передать данные своего профиля
стороннему сайту. С помощью этого параметра в связке с $options приложение из
примера получает на некоторое время действующий $token доступа к API Facebook.

Интерфейс любого сервиса содержит два обязательных метода:

    abstract public function retrieveToken(/*string*/ $code);
    abstract public function retrieveProfile(\FastAuth\Token $token);

Первый возвращает переменную типа \FastAuth\Token либо генерирует одно из исключений:

    \FastAuth\Exception\BadRequest
    \FastAuth\Exception\ServerError

Второй возвращает профиль пользователя \FastAuth\Profile полученный с удалённого ресурса, либо одно из исключений:

    \FastAuth\Exception\BadRequest
    \FastAuth\Exception\ServerError


Сервисы
-------

Зарегистрировать свои приложения можно по следующим URL:  
[Facebook](https://developers.facebook.com/apps)  
[Google](https://code.google.com/apis/console/)  
[ВКонтакте](http://vk.com/editapp?act=create)  
[Mail.ru](http://api.mail.ru/sites/my/add)  
[Одноклассники](http://dev.odnoklassniki.ru/wiki/pages/viewpage.action?pageId=13992188)  
[Yandex](https://oauth.yandex.ru/client/new)  
[Windows Live](https://manage.dev.live.com/AddApplication.aspx?tou=1)  
[Bitly](https://bitly.com/a/create_oauth_app)  
[Foursquare](https://ru.foursquare.com/developers/register)  
[GitHub](https://github.com/settings/applications/new)  
[PayPal](https://devportal.x.com/sdm/set_app/Production)  

Опции
-----

    'vkontakte' => array(
        'client_id' => '<CONSUMER_KEY>',
        'secret' => '<CONSUMER_SECRET>',
        'redirect_uri' => '<REDIRECT_URI>',
        'public_link' => 'https://oauth.vk.com/authorize?client_id=<CONSUMER_KEY>&scope=&redirect_uri=<REDIRECT_URI>&response_type=code',
    ),
    'google' => array(
        'client_id' => '<CONSUMER_KEY>',
        'secret' => '<CONSUMER_SECRET>',
        'redirect_uri' => '<REDIRECT_URI>',
        'public_link' => 'https://accounts.google.com/o/oauth2/auth?client_id=<CONSUMER_KEY>&redirect_uri=<REDIRECT_URI>&scope=https://www.googleapis.com/auth/userinfo.profile%20https://www.googleapis.com/auth/userinfo.email&response_type=code',
    ),
    'facebook' => array(
        'client_id' => '<CONSUMER_KEY>',
        'secret' => '<CONSUMER_SECRET>',
        'redirect_uri' => '<REDIRECT_URI>',
        'public_link' => 'https://www.facebook.com/dialog/oauth?client_id=<CONSUMER_KEY>&redirect_uri=<REDIRECT_URI>&scope=email,user_birthday,user_hometown,user_interests,user_location,user_website',
    ),
    'mailru' => array(
        'client_id' => '<CONSUMER_KEY>',
        'private' => '<CONSUMER_PRIVATE>',
        'secret' => '<CONSUMER_SECRET>',
        'redirect_uri' => '<REDIRECT_URI>',
        'public_link' => 'https://connect.mail.ru/oauth/authorize?client_id=<CONSUMER_KEY>&response_type=code&redirect_uri=<REDIRECT_URI>',
    ),
    'odnoklassniki' => array(
        'client_id' => '<CONSUMER_KEY>',
        'public' => '<CONSUMER_PUBLIC>',
        'secret' => '<CONSUMER_SECRET>',
        'redirect_uri' => '<REDIRECT_URI>',
        'public_link' => 'http://www.odnoklassniki.ru/oauth/authorize?client_id=<CONSUMER_KEY>&response_type=code&redirect_uri=<REDIRECT_URI>',
    ),
    'yandex' => array(
        'client_id' => '<CONSUMER_KEY>',
        'secret' => '<CONSUMER_SECRET>',
        'redirect_uri' => '<REDIRECT_URI>',
        'public_link' => 'https://oauth.yandex.ru/authorize?response_type=code&client_id=<CONSUMER_KEY>',
    ),
    'live' => array(
        'client_id' => '<CONSUMER_KEY>',
        'secret' => '<CONSUMER_SECRET>',
        'redirect_uri' => '<REDIRECT_URI>',
        'public_link' => 'https://oauth.live.com/authorize?client_id=<CONSUMER_KEY>&scope=wl.basic,wl.emails&response_type=code&redirect_uri=<REDIRECT_URI>',
    ),
    'bitly' => array(
        'client_id' => '<CONSUMER_KEY>',
        'secret' => '<CONSUMER_SECRET>',
        'redirect_uri' => '<REDIRECT_URI>',
        'public_link' => 'https://bitly.com/oauth/authorize?client_id=<CONSUMER_KEY>&redirect_uri=<REDIRECT_URI>',
    ),
    'foursquare' => array(
        'client_id' => '<CONSUMER_KEY>',
        'secret' => '<CONSUMER_SECRET>',
        'redirect_uri' => '<REDIRECT_URI>',
        'public_link' => 'https://foursquare.com/oauth2/authenticate?client_id=<CONSUMER_KEY>&response_type=code&redirect_uri=<REDIRECT_URI>',
    ),
    'github' => array(
        'client_id' => '<CONSUMER_KEY>',
        'secret' => '<CONSUMER_SECRET>',
        'redirect_uri' => '<REDIRECT_URI>',
        'public_link' => 'https://github.com/login/oauth/authorize?client_id=<CONSUMER_KEY>&redirect_uri=<REDIRECT_URI>',
    ),
    'paypal' => array(
        'client_id' => '<CONSUMER_KEY>',
        'secret' => '<CONSUMER_SECRET>',
        'redirect_uri' => '<REDIRECT_URI>',
        'public_link' => '',
    ),

