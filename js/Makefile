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
	manage_user \
	manage_ufs \
	report_account \
	report_order \
	report_stats \
	shop_and_order \
	validate

CANONICAL_DEPS = \
	jquery/jquery.js \
	jquery/jquery-ui-1.8.custom.min.js \
	fgmenu/fg.menu.js \
	jquery/jquery.aixadaMenu.js \
        jquery/jquery.aixadaXML2HTML.js \
        jquery/jquery.aixadaUtilities.js

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
	jquery/jquery-ui-1.8.custom.min.js \
        jquery/jquery.aixadaXML2HTML.js \
        jquery/jquery.aixadaUtilities.js
	cat $^ > $@

js_for_incidents.js: \
	$(CANONICAL_DEPS) 
	cat $^ > $@

js_for_index.js: \
	jquery/jquery.js \
	jquery/jquery-ui-1.8.custom.min.js \
	fgmenu/fg.menu.js \
	jquery/jquery.aixadaMenu.js 
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
        aixadacart/jquery.aixadacart.js \
        jquery/jquery.sparkline-1.5.1.min.js
	cat $^ > $@

js_for_validate.js: \
	$(CANONICAL_DEPS) \
        aixadacart/jquery.aixadacart.js
	cat $^ > $@

js_for_manage_dates.js: \
	$(CANONICAL_DEPS) 
	cat $^ > $@

js_for_manage_user.js: \
	$(CANONICAL_DEPS) 
	cat $^ > $@

js_for_manage_ufs.js: \
	$(CANONICAL_DEPS) 
	cat $^ > $@


clean: 
	rm -f *js *~