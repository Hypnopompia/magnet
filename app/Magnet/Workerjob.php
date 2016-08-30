<?php
namespace App\Magnet;

use AWS;
use Config;

class Workerjob {
	protected $entries = [];

	public function addJob($jobname, $jobdata) {
		$id = time() . "_" . count($this->entries) . "_" . $jobname;
		$this->entries[] = [
			'Id' => $id,
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
}
