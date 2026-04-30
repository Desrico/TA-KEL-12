<?php
use Illuminate\Support\Facades\DB;

$journals = DB::connection('mongodb')->table('journal_texts')->pluck('description')->take(10)->toArray();
file_put_contents('scratch/journal_samples.txt', print_r($journals, true));
echo "Samples written to scratch/journal_samples.txt\n";
