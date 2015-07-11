config_dir:=local_config

all: canned_responses%.php sql/all
#sql/all css/all


sql/all:
	$(MAKE) -C sql


canned_responses%.php: sql/aixada.sql php/utilities/tables.php $(wildcard $(config_dir)/lang/*) \
	php/lib/table_with_ref.php php/inc/name_mangling.php make_canned_responses.php \
	$(config_dir)/config.php
	php make_canned_responses.php

clean: clean_ship 
	rm -f canned_responses_*.php
	touch $(config_dir)/config.php

very_clean: clean js/clean sql/clean css/clean 
	touch sql/aixada.sql

