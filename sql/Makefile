QUERIES = $(wildcard queries/*sql)

all: aixada.png setup/all \
     database-definition.up-to-date database-queries.up-to-date

##aixada.png: aixada.sql
##	sqlt-diagram -d=MySQL aixada.sql -o aixada.png --color

setup/all:
	$(MAKE) -C setup

database-definition.up-to-date: aixada.sql setup/aixada_setup_details.sql $(wildcard migration/*sql)
	mysql -e "source ../local_config/aixada_setup.sql; source aixada_migrate.sql;" 
	touch database-definition.up-to-date

database-queries.up-to-date: $(QUERIES)
	mysql -e "use aixada; source aixada_setup_queries.sql;"
	touch database-queries.up-to-date

clean: 
	$(MAKE) -C setup clean



