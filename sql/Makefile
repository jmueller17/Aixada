QUERIES = $(wildcard queries/*sql)
DB_NAME = aixadac
DB_USER = aixada
DB_PASSWD = aixada
DB_DUMP = aixada_current_dump.sql


all: aixada.png setup/all database-definition.up-to-date database-queries.up-to-date

##aixada.png: aixada.sql
##	sqlt-diagram -d=MySQL aixada.sql -o aixada.png --color

setup/all:
	$(MAKE) -C setup

database-definition.up-to-date: aixada.sql $(DB_DUMP) dbUpgradeToAixada2.5.sql
	mysql --default-character-set=utf8 -u $(DB_USER) --password="$(DB_PASSWD)" -e "drop database $(DB_NAME); create database $(DB_NAME); use $(DB_NAME); source $(DB_DUMP); source dbUpgradeAixada2.1ToAixada2.5.sql;"
	touch database-definition.up-to-date

database-queries.up-to-date: aixada_setup_queries.sql $(QUERIES)
	mysql -u $(DB_USER) --password="$(DB_PASSWD)" -e "use $(DB_NAME); source aixada_setup_queries.sql;"
	touch database-queries.up-to-date

clean: 
	$(MAKE) -C setup clean



