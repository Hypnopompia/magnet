<?php

Route::any('/sqs', 'SQSController@sqs');
Route::any('/sqs.addboardsjobs', 'SQSController@addBoardsJobs');
Route::any('/sqs.addpinsjobs', 'SQSController@addPinsJobs');
