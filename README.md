# Effect Pay

Компонент для онлайн-оплаты через Сбербанк и Робокассу.

[Скачать пакет](packages)

## Подключение

### Shopkeeper

Добавить в форму заказа хук `effectpay.shk.hook`
```
'hooks' => 'shk_fihook,email,effectpay.shk.hook,FormItAutoResponder'
```

В сообщение об успешном заказе и в письмо добавить ссылку на оплату:
```
<$if $.session.shk_pay_link$>
    <br><br>
    <a class="button" target="_blank" href="<$$.session.shk_pay_link$>">Оплатить заказ</a>
<$/if$>
```

или редирект:
```
<$if $.session.shk_pay_link$>
    <script>window.open('<$$.session.shk_pay_link$>', '_blank');</script>
<$/if$>
```

Вывод ошибок (для Сбербанка):
```
<$if $.session.shk_pay_error$>
    <br><br>
    <div class="error"><$$.session.shk_pay_error$></div>
<$/if$>
```


##  Платёжные системы

### Сбербанк

Shopkeeper: в названии способа оплаты должно быть «sberbank» или «сбербанк».

Callback-уведомления подключаются через техподдержку Сбербанка.
Адрес: `assets/components/effectpay/payment.php?mode=sberbank_callback`.

Тестовые карты, документация: https://securepayments.sberbank.ru/wiki/doku.php/test_cards


### Робокасса

Shopkeeper: в названии способа оплаты должно быть «robokassa»

В личном кабинете сгенерировать 4 пароля, прописать в системные настройки.

Callback-уведомления (Result Url): assets/components/effectpay/payment.php?mode=robokassa_callback. Метод: POST.

Прописать Result Url и задать нетестовые пароли попросить заказчика, так как нужно подтверждение. 

Алгоритм работы:
- Заказу при оформлении присваивается рандомный ключ `[options][pay_key]`.
- Сохраняется ссылка на оплату `[options][pay_link]`: `assets/components/effectpay/payment.php?mode=robokassa_pay&id=$id&key=$key`.
- При переходе по ссылке получаем заказ по id, проверяя key (чтоб кто попало не смог оплатить заказ, зная id).
- Генерируется форма, автоматически происходит сабмит и редирект на страницу оплаты.


## todo

- Добавить описание к настройкам
- Добавить выбор налогообложения и СНО в настройки