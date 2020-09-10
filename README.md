# Effect Pay

Компонент для онлайн-оплаты через Сбербанк, Робокассу, PayKeeper, Альфа-банк.

[Скачать (0.4.3)](https://github.com/web-effect/modx-effect-pay/raw/master/packages/effectpay-0.4.3-alpha.transport.zip)

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
Адрес: `SITE/assets/components/effectpay/payment.php?mode=sberbank_callback`.

Тестовые карты, документация: https://securepayments.sberbank.ru/wiki/doku.php/test_cards


### Робокасса

Shopkeeper: в названии способа оплаты должно быть «robokassa»

В личном кабинете сгенерировать 4 пароля, прописать в системные настройки.

Callback-уведомления (Result Url): assets/components/effectpay/payment.php?mode=robokassa_callback. Метод: POST.

Прописать Result Url и задать нетестовые пароли попросить заказчика, так как нужно подтверждение. 

Алгоритм работы:
- Заказу при оформлении присваивается рандомный ключ `[options][pay_key]`.
- Сохраняется ссылка на оплату `[options][pay_link]`: `SITE/assets/components/effectpay/payment.php?mode=robokassa_pay&id=$id&key=$key`.
- При переходе по ссылке получаем заказ по id, проверяя key (чтоб кто попало не смог оплатить заказ, зная id).
- Генерируется форма, автоматически происходит сабмит и редирект на страницу оплаты.


### PayKeeper

Shopkeeper: в названии способа оплаты должно быть «paykeeper».

Тестовый режим отсутствует.

effectpay.paykeeper.server — адрес ЛК, например `site.server.paykeeper.ru`.

Callback-уведомления (вкладка «Получение информации о платежах»). Способ: POST-оповещения. URL: `SITE/assets/components/effectpay/payment.php?mode=paykeeper_callback`. Сгенерировать секретное слово, задать его в настройках сайта.


### Альфа-банк

Shopkeeper: в названии способа оплаты должно быть «alpha» или «альфа».

Создать страницу «спасибо за заказ», прописать её id в настройку «effectpay.return_page». На странице вызвать сниппет: `[[!effectpay.checkStatus]]`.

Тестовые карты: https://pay.alfabank.ru/ecommerce/instructions/merchantManual/pages/index/test_cards.html

Для боевого режима нужно выполнить требования (по ссылке) и сделать заявку.
https://pay.alfabank.ru/ecommerce/instructions/merchantManual/pages/index/script.html


## todo

- Добавить описание к настройкам
- Добавить выбор налогообложения и СНО в настройки