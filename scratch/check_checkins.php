<?php
use Illuminate\Support\Facades\DB;
$d = DB::connection('mongodb')->table('daily_checkins')->first();
print_r($d);
