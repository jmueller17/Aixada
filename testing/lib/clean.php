<?php

exec("rm -f {$query_dump_dir}*bare_log {$query_dump_dir}*annotated_log");
exec("rm -rf {$testrunpath}*; git checkout -- {$testrunpath}README {$reference_dump_dir}README");

?>