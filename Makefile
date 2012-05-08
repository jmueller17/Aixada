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
ship_root:=ship
ship_dir_name:=aixada
migration_ship_dir_name:=aixada_ship_migration

ship_dir:=$(ship_root)/$(ship_dir_name)
migration_ship_dir:=$(ship_root)/$(migration_ship_dir_name)

all: sql/all canned_responses.php css/all js/all $(config_dir)/config.php #sql/dumps/all

sql/all:
	$(MAKE) -C sql

css/all:
	$(MAKE) -C css

js/all:
	$(MAKE) -C js

canned_responses.php: sql/aixada.sql utilities_tables.php $(wildcard $(config_dir)/lang/*) \
	lib/table_with_ref.php
	#php make_canned_responses.php

$(config_dir)/config.php: sql/setup/queries_reading.php \
            sql/setup/tables_modified_by.php \
            make_config.php
	php make_config.php

clean_ship:
	rm -rf $(ship_dir)
	rm -f *~

clean_ship_migration:
	rm -rf $(migration_ship_dir)
	rm -f *~

clean: clean_ship 
	rm -f canned_responses_*.php
	touch $(config_dir)/config.php

very_clean: clean js/clean sql/clean css/clean 
	touch sql/aixada.sql

dirs = css/ css/jquery-ui css/jquery-ui/ui-lightness css/jquery-ui/ui-lightness/images \
	img inc js js/i18n js/aixadacart js/aixadacart/i18n js/jquery js/fgmenu \
	lib sql sql/setup \
	sql/queries $(config_dir) $(config_dir)/lang 

firephpdirs = FirePHPCore FirePHPCore/lib FirePHPCore/lib/FirePHPCore

uncompressed_files = \
	js/fgmenu/fg.menu.js \
	js/jquery/jquery.aixadaMenu.js \
	js/jquery/jquery.aixadaUtilities.js 

files = $(wildcard ctrl*) \
	activate_products.php \
	activate_all_roles.php \
	activate_roles.php \
	arrived_products.php \
	$(wildcard canned_responses_*.php) \
	$(wildcard css/*min.css) css/aixada_main.css css/print.css css/ui.jqgrid.css \
	css/jquery-ui/ui-lightness/jquery-ui-1.8.custom.css \
	$(wildcard css/jquery-ui/ui-lightness/images/*) \
	$(wildcard img/*) \
	$(wildcard inc/*) \
	incidents.php \
	index.php \
	install.php \
	$(wildcard js/js*min.js) \
	js/aixadacart/aixadacart.css \
	js/aixadacart/jquery.aixadacart.min.js \
	js/aixadacart/jquery.aixadacart.js \
	js/jquery/jquery-1.4.4.min.js \
	js/jquery/jquery.js \
	js/jquery/jquery.aixadaMenu.min.js \
	js/jquery/jquery.aixadaUtilities.min.js \
	js/jquery/jquery.aixadaXML2HTML.min.js \
	js/jquery/jquery.jqGrid.min.js \
	js/jquery/jquery.sparkline-1.5.1.min.js \
	js/jquery/jquery-ui-1.8.custom.min.js \
	js/jquery/jquery.aixadaXML2HTML.js \
	js/fgmenu/fg.menu.css \
	$(wildcard js/aixadacart/i18n/*) \
	$(wildcard js/i18n/*) \
	$(wildcard lib/*) \
	LICENSE \
	INSTALL \
	login.php \
	$(wildcard manage_*) \
	all_prevorders.php \
	README \
	$(wildcard report_*) \
	shop_and_order.php \
	smallqueries.php \
	sql/aixada_setup_queries.sql \
	sql/setup/aixada_setup_details.sql \
	local_config/aixada_setup.sql \
	sql/aixada.sql \
	$(wildcard sql/queries/*) \
	$(wildcard utilities*) \
	validate.php \
	$(uncompressed_files)

config_files = $(wildcard $(config_dir)/lang/*) $(config_dir)/config.php

#pat = ""
pat="s:require_once('FirePHPCore/lib/FirePHPCore/FirePHP.class.php');::g;
pat+=s:ob_start://ob_start:g; 
pat+=s:global \$$firephp://global \$$firephp:g; 
pat+=s:\$$firephp://\$$firephp:g; "

ship_targets:=all clean_ship clean_ship_migration

# The following is specially for installing the Aixada database using the target 
# "make ship_migration". It has no effect with "make ship".

export DUMP_TABLES=\
	aixada_uf \
	aixada_member \
	aixada_user \
	aixada_user_role \
	aixada_provider \
	aixada_product_category \
	aixada_orderable_type \
	aixada_unit_measure \
	aixada_rev_tax_type \
	aixada_product 

export DUMP_NAME = aixada.uf_member_user.dump.sql


sql/dumps/all:
	$(MAKE) -C sql/dumps/ 

ship: $(ship_targets)
	mkdir -p $(ship_dir)
	$(foreach dir,$(dirs),mkdir $(ship_dir)/$(dir);)
	$(foreach file,$(files),sed -e $(pat) $(file) > $(ship_dir)/$(file);)
	chmod a+w $(ship_dir)/$(config_dir)
	chmod a+w $(ship_dir)/$(config_dir)/lang
	$(foreach file,$(config_files),sed -e $(pat) $(file) > $(ship_dir)/$(file);)
	find $(ship_dir) -name "*~" -exec rm -f \{\} \;
	find $(ship_dir) -name "#*" -exec rm -f \{\} \;
	chmod a+w $(ship_dir)/$(config_dir)/*
	chmod a+w $(ship_dir)/$(config_dir)/lang/*
	cd $(ship_root); tar cvfj $(ship_dir_name).tar.bz2 --preserve-permissions $(ship_dir_name)/*

ship_migration: $(ship_targets) sql/dumps
	mkdir -p $(migration_ship_dir)
	$(foreach dir,$(dirs) $(firephpdirs) sql/dumps,mkdir $(migration_ship_dir)/$(dir);)
	$(foreach file,$(files) FirePHPCore/lib/FirePHPCore/FirePHP.class.php sql/dumps/$(DUMP_NAME),sed -e $(pat) $(file) > $(migration_ship_dir)/$(file);)
	chmod a+w $(migration_ship_dir)/$(config_dir)
	chmod a+w $(migration_ship_dir)/$(config_dir)/lang
	$(foreach file,$(config_files),sed -e $(pat) $(file) > $(migration_ship_dir)/$(file);)
	chmod a+w $(migration_ship_dir)/$(config_dir)/*
	chmod a+w $(migration_ship_dir)/$(config_dir)/lang/*
	cd $(ship_root); tar cvfz $(migration_ship_dir_name).tar.gz --preserve-permissions $(migration_ship_dir_name)/*
