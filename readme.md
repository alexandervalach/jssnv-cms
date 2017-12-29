# JSSNV

## Systémové požiadavky
Pripravíme si čistý ubuntu server 16.04

Potrebné balíky:
```
sudo apt-get install apache2 php git libapache2-mod-php
```

Aktivujeme potrebné apache moduly:
```
sudo a2enmod rewrite ssl
sudo service apache2 restart
```

## Postup pri inštalácii kopírovaním
Projekt nakopírujeme do `/var/www/jssnv` s tým, že všetky súbory sú čitateľné pre 
používateľa `www-data`, ideálne ak je ich vlastníkom.
Adresáre `temp` a `log` musia byť pre používateľa `www-data` aj zapisovateľné.

## Postup inštalácie z git repozitára
Ako používateľ `root` vytvoríme používateľovi `www-data` adresár pre ssh kľúče. 
```
sudo mkdir /var/www/.ssh
sudo chown www-data:www-data /var/www/.ssh
```

Používateľovi `www-data` vygenerujeme nový RSA kľúč. Všetky parametre potvrdíme 
bez zmeny (umiestnenie, názov, heslo).

```
sudo -u www-data ssh-keygen -t rsa
```

Verejnú časť RSA kľúča - obsah súboru `id_rsa.pub` vložíme medzi deploy kľúče 
v danom repozitári.

Vytvoríme adresár pre aplikáciu, vlastníkom bude používateľ `www-data`.

```
sudo mkdir /var/www/jssnv
sudo chown www-data:www-data /var/www/jssnv
```

Vyklonujeme repozitár ako používateľ `www-data`.
```
sudo -u www-data -H git clone -b master git@bitbucket.org:alexandervalach/sahl.git /var/www/jssnv
```

## Postup aktualizácie z git repozitára
Stiahneme aktuálne zdrojáky a zmažeme cache.
```
sudo -u www-data -H git pull
sudo rm -r /var/www/jssnv/temp/cache
```
## Konfigurácia apache2
Do `/etc/apache2/sites-available/jssnv.conf` pridáme nasledujúci obsah.

```
#!apacheconf

<VirtualHost *:80>
        
        DocumentRoot /var/www/jssnv/www
		ServerName jssnv

        <Directory "/var/www/jssnv/www">
                AllowOverride All
        </Directory>

</VirtualHost>
```

Reštartujeme apache
```
sudo service apache2 restart
```