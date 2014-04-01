PHPFILES = $(wildcard *_for_*.php)
MINCSSFILES = $(patsubst %.php,%.min.css,$(PHPFILES))

all: $(MINCSSFILES)

%.min.css : %.php
	php $< > $*
	yui-compressor --type css $* > $@
	rm -f $*

clean: 
	rm *min.css