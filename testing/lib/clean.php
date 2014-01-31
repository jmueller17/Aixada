<?php

exec("rm -f {$query_dump_dir}*; git checkout -- {$query_dump_dir}README");
exec("rm -rf {$testrunpath}*; git checkout -- {$testrunpath}README {$reference_dump_dir}README");

?>