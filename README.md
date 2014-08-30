# CronMail to Gelf

Get cron mail from imap and send them with gelf 

## Prerequisite

The mail from cron need to be in a "Cron" mailbox.

## Usage

```
php command.php
```

## Information

Each line of the mail is sent with this information :
* level : notice
* host : retrieve from the headers
* message : the line
* facility : cron-email
* additionals : uid and subject of the mail
