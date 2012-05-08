QUERIES = aixada_setup_details.sql $(wildcard ../queries/*sql)

all: queries_reading.php tables_modified_by.php make_query_dependencies.php

queries_reading.php tables_modified_by.php : $(QUERIES) \
        make_query_dependencies.php
	php make_query_dependencies.php

clean:
	rm -f query_dependencies.php *~
