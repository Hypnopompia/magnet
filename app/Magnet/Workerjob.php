<?php
namespace App\Magnet;

use AWS;
use Config;

class Workerjob {
	protected $entries = [];

	public function addJob($jobname, $jobdata, $delaySeconds = 0) {
		$id = time() . "_" . count($this->entries) . "_" . $jobname;
		$this->entries[] = [
			'Id' => $id,
			'DelaySeconds' => $delaySeconds,
			'MessageBody' => json_encode([
				'jobname' => $jobname,
				'jobdata' => $jobdata
			])
		];

		if (count($this->entries) == 10) {
			$this->send();
		}

		return $this;
	}

	public function send() {
		$sqs = AWS::createClient('sqs');
		if (is_array($this->entries) && count($this->entries) > 0) {
			$result = $sqs->sendMessageBatch([
				'QueueUrl' => Config::get("magnet.sqsQueueUrl"),
				'Entries' => $this->entries
			]);
		}
		$this->entries = [];
	}

	public static function sendJob($jobname, $jobdata, $delaySeconds = 0) {
		$workerjob = new Workerjob();
		$workerjob->addJob($jobname, $jobdata, $delaySeconds)->send();
		return true;
	}
}
