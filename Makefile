# Uncomment the line containing the FINAL variable to indicate that
# you will be building a production version.
#
# One consequence of this is that you will get real minified .js files.
# If the line is commented, files with the extension .min.js are still created,
# but they are really only concatenated javascript files. This still
# gives some efficiency gain in the browser because only one file has to be 
# downloaded.
#
# The next nontrivial line is the one you should uncomment:
#
export FINAL = true
#

config_dir:=local_config

all: canned_responses.php $(config_dir)/config.php 
#sql/all js/all css/all


# sql/all:
# 	$(MAKE) -C sql

# css/all:
# 	$(MAKE) -C css

# js/all:
# 	$(MAKE) -C js

canned_responses.php: sql/aixada.sql php/utilities/tables.php $(wildcard $(config_dir)/lang/*) \
	lib/table_with_ref.php
	php make_canned_responses.php

#$(config_dir)/config.php: sql/setup/queries_reading.php \
#            sql/setup/tables_modified_by.php \
#            make_config.php
#	php make_config.php

clean_ship:
	rm -rf $(ship_dir)
	rm -f *~

clean: clean_ship 
	rm -f canned_responses_*.php
	touch $(config_dir)/config.php

very_clean: clean js/clean sql/clean css/clean 
	touch sql/aixada.sql

