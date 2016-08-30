<?php

Route::any('/sqs', 'SQSController@sqs');
Route::any('/sqs.addjobs', 'SQSController@addjobs');
