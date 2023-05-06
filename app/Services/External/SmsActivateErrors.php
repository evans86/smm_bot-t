<?php

namespace App\Services\External;

class SmsActivateErrors extends \Exception
{
    private $responseCode;

    public function __construct($errorCode)
    {
        $this->responseCode = $errorCode;
        $message = "{$this->errorCodes[$errorCode]}";
        parent::__construct($message);
    }

    public function checkExist($errorCode)
    {
        return array_key_exists($errorCode, $this->errorCodes);
    }

//    protected $errorCodes = array(
//        'ACCESS_ACTIVATION' => 'Сервис успешно активирован',
//        'ACCESS_CANCEL' => 'активация отменена',
//        'ACCESS_READY' => 'Ожидание нового смс',
//        'ACCESS_RETRY_GET' => 'Готовность номера подтверждена',
//        'ACCOUNT_INACTIVE' => 'Свободных номеров нет',
//        'ALREADY_FINISH' => 'Аренда уже завершена',
//        'ALREADY_CANCEL' => 'Аренда уже отменена',
//        'BAD_ACTION' => 'Некорректное действие (параметр action)',
//        'BAD_SERVICE' => 'Некорректное наименование сервиса (параметр service)',
//        'BAD_KEY' => 'Неверный API ключ доступа',
//        'BAD_STATUS' => 'Попытка установить несуществующий статус',
//        'BANNED' => 'Аккаунт заблокирован',
//        'CANT_CANCEL' => 'Невозможно отменить аренду (прошло более 20 мин.)',
//        'ERROR_SQL' => 'Один из параметров имеет недопустимое значение',
//        'NO_NUMBERS' => 'Нет свободных номеров для приёма смс от текущего сервиса',
//        'NO_BALANCE' => 'Закончился баланс',
//        'NO_YULA_MAIL' => 'Необходимо иметь на счету более 500 рублей для покупки сервисов холдинга Mail.ru и Mamba',
//        'NO_CONNECTION' => 'Нет соединения с серверами sms-activate',
//        'NO_ID_RENT' => 'Не указан id аренды',
//        'NO_ACTIVATION' => 'Указанного id активации не существует',
//        'STATUS_CANCEL' => 'Активация/аренда отменена',
//        'STATUS_FINISH' => 'Аренда оплачена и завершена',
//        'STATUS_WAIT_CODE' => 'Ожидание первой смс',
//        'STATUS_WAIT_RETRY' => 'ожидание уточнения кода',
//        'SQL_ERROR' => 'Один из параметров имеет недопустимое значение',
//        'INVALID_PHONE' => 'Номер арендован не вами (неправильный id аренды)',
//        'INCORECT_STATUS' => 'Отсутствует или неправильно указан статус',
//        'WRONG_SERVICE' => 'Сервис не поддерживает переадресацию',
//        'WRONG_SECURITY' => 'Ошибка при попытке передать ID активации без переадресации, или же завершенной/не активной активации'
//    );

    public function getResponseCode()
    {
        return $this->errorCodes[$this->responseCode];
    }
}
