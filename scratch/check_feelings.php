<?php
use Illuminate\Support\Facades\DB;
$f = DB::connection('mongodb')->table('feelings')->first();
print_r($f);
