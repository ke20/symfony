Fake SMS Notifier
=================

Provides Fake SMS integration for Symfony Notifier.

The fake sms integration could :
 - Send sms by email using the email integration
 - Save sms in a database using the database integration

Email integration
---------

#### DSN example

```
FAKE_SMS_DSN=fakesms+email://default?to=TO&from=FROM
```

where:
 - `TO` is email who receive SMS during development
 - `FROM` is email who send SMS during development

To use a custom mailer transport:
```
FAKE_SMS_DSN=fakesms+email://mailchimp?to=TO&from=FROM
```

Database integration
---------

#### DSN example

```
FAKE_SMS_DSN=fakesms+database://default?to=TO&from=FROM&entity=ENTITY_CLASS
```

where:
- `ENTITY_CLASS` is the entity resource class (Should implement `Symfony\Component\Notifier\Bridge\FakeSms\SmsInterface`)
- `TO` is the phone number which receive SMS during development
- `FROM` is the phone number or provider name which send SMS during development

Resources
---------

  * [Contributing](https://symfony.com/doc/current/contributing/index.html)
  * [Report issues](https://github.com/symfony/symfony/issues) and
    [send Pull Requests](https://github.com/symfony/symfony/pulls)
    in the [main Symfony repository](https://github.com/symfony/symfony)
