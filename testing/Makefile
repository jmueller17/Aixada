all: test.log clean.log 

clean.log: ../aixada.log non_modifying_patterns
	egrep -iv -f non_modifying_patterns  $< > $@

test.log: clean.log ../aixada.log non_modifying_patterns
	cd .. && php testing/make_test_log.php 





