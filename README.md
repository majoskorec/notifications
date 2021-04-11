# Notifikační služba (microservice)

## Stručný popis problému

### Business požadavky
Ze systému potřebujeme posílat různé typy zpráv/notifikací např.:
- emailové zprávy/notifikace
- sms
- push notifikace
- automatizované volání (např. Asterisk ústředna anebo Twilio)

Pro různé typy zpráv uvažujeme různé poskytovatele např. emaily můžeme posílat přes postfix/sendmail
anebo přes služby jako Mailchimp, Sendgrid apod. - kvůli marketingovým možnostem, doručitelnosti a také ceně.
Pro jednoduchost uvažujme, že máme nakonfigurovaného právě jednoho poskytovatele pro daný typ zprávy.

Zprávy/notifikace můžeme odesílat z více systémů, které mohou být na sobě nezávislé. Můžeme chtít odesílat již připravený text
anebo naopak použít pro zprávu šablonu a naplnit ji z předaných proměnných.

### Technické požadavky
- PHP 7.4 a vyšší, volitelný framework např. Laravel/Lumen, Symfony, Slim
- libovolný databázový backend - neuvažujeme retenci dat x let dozadu, pro odeslané zprávy nám stačí max. půl roku
- authentifikace - machine to machine např. oAuth2 ...
- jednoduché REST API pro vytvoření notifikace/zprávy, API chceme vědět verzovat
- backend, který je bude přes frontu notifikace/zprávy odesílat (semisync nebo async)
- možnost použít webhooky při úspěšném nebo neúspěšném odeslání zprávy
- deployment uvažujeme na Azure, AWS anebo do nějakého hybridního cloudu
- umožnit monitoring aplikace (app status)

## Předpokládaný výstup

### Analýza
Na úvod nějaká stručná analýza - stačí v pár bodech rozebrat vybrané řešení.
Možno zde upozornit na předpokládáné problémy a limita vybraného řešení.

### Kostra implementace
> Netřeba napsat celou službu a naimplementovat všechny uvažované možnosti. Stačí kostra projektu podle bodů níže:
- [ ] založit projekt
- [ ] založit kostru datového modelu
- [ ] naimplementovat kostru přijetí a zpracování zprávy
- [ ] zkusit naimplementovat jeden adaptér pro volitelný typ zprávy
- [ ] nastínit, jakým způsobem možno monitorovat zdraví celého aplikace

# Riesenie

- auth som velmi neriesil, ta je v symfony velmi jednoducho configurovatelna (symfony/security-bundle)


- routa na zaklade verzie a typu notifikacie bude mat zadefinovane aky objekt ocakava a ktora sluzba ju spracuje
- po validacii spravy sa ta ulozi do MQ na async spracovanie
- MQ podporuje aj delay v posielani takze cez options sa da nastavit aj cas kedy sa ma notifikacia poslat


- consumer pracovava jednotlive message a posiela ich do svojit sendrov
- sender vyuziva konkretne adaptery na posielanie notifikacie
- tu je moznost rozsirit logiku pridelenia adaptera:
  - cez admin rozhranie
  - cez detekciu vytazenosti ci nedostupnosti
  - cez metadata priamo v notifikacii
- o pripadny retry sa stara symfony mq automaticky podla konfiguracie transportu
  - tu moze nastat problem ak by sme potrebovali tuto konfiguraciu riesit dajak dynamicku (ale mozno to symfony vie ;])
- o failed message sa stara samostatny consumer

Implementovane su dva typy notifikacii:
- email: standardny email
- email_template: client posiela nazov preddefinovanej templatu 

## Configuration

- ak chcete realne posielat emaile tak vytvorit `.env.local` s `MAILER_URL` https://symfony.com/doc/current/email.html#configuration
- spustit docker compose `docker-compose up -d`
- v fpm containery spustit consumera
  - `docker exec -it notifications-fpm bash`
  - `bin/console messenger:consume async`

## Tech Spec
- symfony 5.2 (PHP 8)
- symfony/messenger
  - async spracovanie notifiakacii
  - aktualne je implementovana symfony/doctrine-messenger ale pomerne jednoducho sa to da nahradit inou sluzbou (napr symfony/amazon-sqs-messenger)  
  - https://symfony.com/doc/current/messenger.html
- symfony/security-bundle
  - aktualne je implementovana len basic auth s in memory usermi (app1 heslo app1 a app2 heslo app2) `config/packages/security.yaml`
  - pre oauth2 sa da vyuzit https://github.com/knpuniversity/oauth2-client-bundle
  - pripadne custom auth https://symfony.com/doc/current/security/guard_authentication.html
  
## Typy notifikacii

Kazdy typ notifikacie musi mat zadefinovany:

**Model** v `/config/services.yaml`: objekt ktory posiela client. Musi implementovat `App\Notification\Model\Notification`

```yaml
parameters:
  notifications:
    - App\Notification\Type\Email\v1\Model\EmailNotification
    - App\Notification\Type\EmailTemplate\v1\Model\EmailTemplateNotification
```

a **Sender**, sluzbu ktora posiela danu notifikaciu. Musi implementovat `App\Notification\Sender\Sender`

Pre Email je to `App\Notification\Type\Email\v1\EmailSender`. 
Ten vyuziva `App\Notification\Type\Email\v1\Adapter\EmailAdapter` na implementovanie konkretnej mailovej sluzby.

## Flow

Client posle Request na `/api/{versionName}/notification/{typeName}`

Request:

```
curl --location --request POST 'http://localhost:8001/api/v1/notification/email' \
--header 'Authorization: Basic YXBwMjphcHAy' \
--header 'Content-Type: application/json' \
--data-raw '{"subject":"hi", "to":"majoskorec@gmail.com","body":"body text", "from": "test@majoskorec.sk", "options": {"response_webhook": "http://majoskorec.sk", "send_time":"2021-04-11 16:00:00 +0200"}}
```
alebo
```
curl --location --request POST 'http://localhost:8001/api/v1/notification/email_template' \
--header 'Authorization: Basic YXBwMjphcHAy' \
--header 'Content-Type: application/json' \
--data-raw '{"subject_template":"subject1", "subject_template_params":{"name":"jozko"}, "to":"majoskorec@gmail.com","body_template":"body1", "body_template_params":{"param1":"text"}, "from": "test@majoskorec.sk", "options": {"response_webhook": "http://majoskorec.sk", "send_time":"2021-04-11 16:00:00 +0200"}}'
```

na `App\Controller\Api\NotificationController` sa zdetekuje type a verzia notifikacie a ulozi sa message do MQ.
**Tu by bolo este nutne doimplementovat validaciu objektov notifikacii.**

Consumer `php bin/console messenger:consume async` spracovava jednotlive message (https://symfony.com/doc/current/messenger.html#consuming-messages-running-the-worker);

Deserializuje notifikaciu a posle ju na spracovanie prisusnej `App\Notification\Sender\Sender` sluzbe.

Ak vsetko zbehne OK a v notifikacii je aj webhook tak sa nakonci posle `App\Notification\Message\Consumer::sendSuccessResponseToWebhook`

V pripade chyby sa MQ snazi opakovat posielanie spravy: https://symfony.com/doc/current/messenger.html#retries-failures

Po X pokusoch sa sprava prepne do `failed` transportu, ktory sa spracovava pomocou `php bin/console messenger:consume failed`.
Ak ma notifikacia nastaveneny `webhook` tak sa posle `App\Notification\Message\FailedConsumer::sendErrorResponseToWebhook`

## Monitoring

dporucam monitorvat:

- na applikacii:
  - failed message:
    - do `App\Notification\Message\FailedConsumer` teda pridat logovanie
    - to ze sa notifikacia dostala do failed pravdepodobne znmena to ze je chyba v sluzbe na poslanie notifikaciu
      - je tam moznost aby sa to este skusilo poslat dakou alternativnou sluzbou 
  - pocet nespracovanych sprav v async
    - v pripade ze prekroci daku hranicu je mozne automaticke naskalovanie php consumera
- na infre
  - standardne veci ako vytazenie php masin, db (mq storagu), ...
    - jednotlive consumery a requesty su na sebe nezavysle, takze nieje problem so skalovanim
