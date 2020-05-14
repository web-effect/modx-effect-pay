# Effect Pay

Компонент для онлайн-оплаты.

[Скачать пакет](packages/effectpay-0.1.0-alpha.transport.zip)

## Подключение

### Shopkeeper

Добавить в форму заказа хук **effectpay.shk.hook**
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

Тут же можно вывести ошибки (для Сбербанка):
```
<$if $.session.shk_pay_error$>
    <br><br>
    <div class="error"><$$.session.shk_pay_error$></div>
<$/if$>
```


##  Платёжные системы

### Robokassa

Для Shopkeeper: в названии способа оплаты должно быть «robokassa»

В личном кабинете сгенерировать 4 пароля, прописать в системные настройки.

Callback-уведомления (Result Url): assets/components/effectpay/payment.php?mode=robokassa_callback. Метод: POST.

Прописать Result Url и задать нетестовые пароли попросить заказчика, так как нужно подтверждение. 

Алгоритм работы:
- Заказу при оформлении присваивается рандомный ключ (`[options][pay_key]`).
- Сохраняется ссылка на оплату (`[options][pay_link]`): `assets/components/effectpay/payment.php?mode=robokassa_pay&id=$id&key=$key`.
- При переходе по ссылке получаем заказ по id, проверяя key (чтоб кто попало не смог оплатить заказ, зная id).
- Генерируется форма, автоматически происходит сабмит и редирект на страницу оплаты.