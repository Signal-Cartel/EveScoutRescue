# EveScoutRescue
Web site and data tools for [evescoutrescue.com](https://evescoutrescue.com/home/).

## Steps to setup a development system
* Create a new database scheme with the following structure: [ESR DB SCHEMA](https://evescoutrescue.com/esr_db_schema.sql) (>= MySQL 5.6 required)
* Copy the folders and files to the web site (ignore .git*, .settings, .project, .buildpath).
* Create a local "esr_dbconfig.ini" with the following entries:
```
hostname=<dbHostname>
username=<dbUsername>
password=<dbPassword>
dbname=<dbName>
```
* Place config file one level above the "esrc" directory (out of range for web server document root).
* Import the wormhole data.
* Import a data dump if you want to work with real data. Otherwise prepare some data on your own (we should provide a small demo data generate script). 
