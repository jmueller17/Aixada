#
# Modify the file lists in the js_for*.js entries if you want to include 
# other javascript files in the corresponding .php files.
#

ifdef FINAL
MINIFY_CMD = yui-compressor
MINIFY_FLAGS = --type js
endif

FILEROOTS = \
	activate_products \
	activate_roles \
	login \
	incidents \
	index \
	manage_dates \
	manage_mysettings \
	manage_ufs \
	report_account \
	report_order \
	report_stats \
	shop_and_order \
	validate

CANONICAL_DEPS = \
	jquery/jquery.js \
	jqueryui/jqueryui.js \
	fgmenu/fg.menu.js \
	aixadautilities/jquery.aixadaMenu.js \
        aixadautilities/jquery.aixadaXML2HTML.js \
        aixadautilities/jquery.aixadaUtilities.js

JSFILES = $(patsubst %,js_for_%.min.js,$(FILEROOTS))

all: $(JSFILES)

%.min.js : %.js
ifdef FINAL
	$(MINIFY_CMD) $(MINIFY_FLAGS) $< > $@
else
	cp $< $@
endif

js_for_activate_products.js: \
	$(CANONICAL_DEPS) 
	cat $^ > $@

js_for_activate_roles.js: \
	$(CANONICAL_DEPS) 
	cat $^ > $@

js_for_login.js: \
	jquery/jquery.js \
	jqueryui/jqueryui.js \
        aixadautilities/jquery.aixadaXML2HTML.js \
        aixadautilities/jquery.aixadaUtilities.js
	cat $^ > $@

js_for_incidents.js: \
	$(CANONICAL_DEPS) 
	cat $^ > $@

js_for_index.js: \
	jquery/jquery.js \
	jqueryui/jqueryui.js \
	fgmenu/fg.menu.js \
	aixadautilities/jquery.aixadaMenu.js 
	cat $^ > $@

js_for_manage_mysettings.js: \
	$(CANONICAL_DEPS) 
	cat $^ > $@

js_for_report_account.js: \
	$(CANONICAL_DEPS) 
	cat $^ > $@

js_for_report_order.js: \
	$(CANONICAL_DEPS) 
	cat $^ > $@

js_for_report_stats.js: \
	$(CANONICAL_DEPS) 
	cat $^ > $@

js_for_shop_and_order.js: \
	$(CANONICAL_DEPS) \
        aixadacart/jquery.aixadacart.js 
	cat $^ > $@

js_for_validate.js: \
	$(CANONICAL_DEPS) \
        aixadacart/jquery.aixadacart.js
	cat $^ > $@

js_for_manage_dates.js: \
	$(CANONICAL_DEPS) 
	cat $^ > $@

js_for_manage_ufs.js: \
	$(CANONICAL_DEPS) 
	cat $^ > $@


clean: 
	rm -f *js *~