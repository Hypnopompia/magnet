<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateImportBoardJobs extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'command:generateimportboardjobs';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Refresh all the users boards';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		foreach (User::all() as $user) {
			dispatch(new ImportBoards($user));
		}
	}
}
