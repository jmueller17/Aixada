QUERIES = $(wildcard queries/*sql)
DB_NAME = aixada
DB_USER = aixada
DB_PASSWD = aixada
DB_DUMP = dump.sql


all: aixada.png database-definition.up-to-date database-queries.up-to-date aixada_queries_all.sql

##aixada.png: aixada.sql
##	sqlt-diagram -d=MySQL aixada.sql -o aixada.png --color

database-definition.up-to-date: aixada.sql $(DB_DUMP)
##	mysql --default-character-set=utf8 -u $(DB_USER) --password="$(DB_PASSWD)" -e "drop database $(DB_NAME); create database $(DB_NAME); use $(DB_NAME); source $(DB_DUMP); source setup/aixada_setup_queries.sql;"
	touch database-definition.up-to-date

database-queries.up-to-date: setup/aixada_setup_queries.sql $(QUERIES)
##	mysql -u $(DB_USER) --password="$(DB_PASSWD)" -e "use $(DB_NAME); source setup/aixada_setup_queries.sql;"
	touch database-queries.up-to-date

aixada_queries_all.sql: $(QUERIES)
	cat $(QUERIES) > setup/aixada_queries_all.sql

clean: 
	$(MAKE) -C setup clean



