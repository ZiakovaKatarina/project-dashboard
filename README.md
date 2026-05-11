<!-- # About

This framework was created to support the teaching of the subject Development of intranet and intranet applications 
(VAII) at the [Faculty of Management Science and Informatics](https://www.fri.uniza.sk/) of
[University of Žilina](https://www.uniza.sk/). Framework demonstrates how the MVC architecture works.

# Instructions and documentation 

The framework source code is fully commented. In case you need additional information to understand,
visit the [WIKI stránky](https://github.com/thevajko/vaiicko/wiki/00-%C3%9Avodn%C3%A9-inform%C3%A1cie) (only in Slovak).

# Docker configuration

The Framework has a basic configuration for running and debugging web applications in the `<root>/docker` directory. 
All necessary services are set in `docker-compose.yml` file. After starting them, it creates the following services:

- web server (Apache) with the __PHP 8.3__ 
- MariaDB database server with a created _database_ named according `MYSQL_DATABASE` environment variable
- Adminer application for MariaDB administration

## Other notes:

- __WWW document root__ is set to the `public` in the project directory.
- The website is available at [http://localhost/](http://localhost/).
- The server includes an extension for PHP code debugging [__Xdebug 3__](https://xdebug.org/), uses the  
  port __9003__ and works in "auto-start" mode.
- PHP contains the __PDO__ extension.
- The database server is available locally on the port __3306__. The default login details can be found in `.env` file.
- Adminer is available at [http://localhost:8080/](http://localhost:8080/) -->


# Systém na správu úloh a projektov
Semestrálna práca z predmetu VAII (Vývoj aplikácií pre internet a intranet).

## Funkcionality
Tento projekt slúži na evidenciu projektov a ich úloh. Používatelia môžu vytvárať projekty, pridávať k nim konkrétne úlohy, komentovať ich a nahrávať k nim prílohy (súbory).

### Hlavné funkcionality
Aplikácia primárne slúži na sprostredkovávanie nasledujúcich funkcionalít:
- Správa úloh (pridávanie, úprava, mazanie)
- Priraďovanie úloh k projektom a užívateľom
- Reporty o plnení úloh

## Inštalácia a spustenie
Aplikácia pre jednoduché spustenie podporuje plné spustenie cez DOCKER.

1. <b>Príprava</b>: Je potrebná inštalácia DOCKER DESKTOPu.
2. <b>Spustenie</b>: V koreňovom priečinku projektu otvorte terminál a zadajte príkaz:
```bash
cd docker; docker-compose up -d; cd ..
```
3. <b>Prístup k webu</b>: Po pár sekundách (kým sa naštartujú kontajnery) bude aplikácia dostupná na adrese: [http://localhost](http://localhost)
<!-- https://stackoverflow.com/questions/66828242/how-to-add-tab-spaces-in-git-readme-between-two-sentences -->
4. <b>Prístup k databáze</b>: Pre potreby prístupu k databáze je možné využiť Adminer dostupný na adrese [http://localhost:8080](http://localhost:8080)
    - Server:&emsp;&emsp;&ensp;'db'
    - Username:&ensp;&nbsp;'vaiicko_user'
    - Password:&emsp;'dtb456'
    - Database:&emsp;'vaiicko_db'
5. <b>Premazanie databázy</b>: V koreňovom priečinku projektu otvorte terminál a zadajte príkaz:
```bash
cd docker; docker-compose down -v; cd ..
```

## Autor
* <b>Katarína Žiaková</b> – [Profil na GitHub-e](https://github.com/ZiakovaKatarina)
* Rok vytvorenia: <b>2026</b>