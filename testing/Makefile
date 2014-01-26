all: test.log clean.log 

clean.log: ../aixada.log util/non_modifying_query_patterns.for_grep 
	egrep -iv -f util/non_modifying_query_patterns.for_grep  $< > $@

test.log: clean.log ../aixada.log non_modifying_patterns
	cd .. && php testing/make_test_log.php 

clean:
	rm *~




