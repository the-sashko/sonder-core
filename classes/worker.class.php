<?php

/**
 * summary
 */
abstract class WorkerCore extends CommonCore
{
	abstract public function run() : void;

	/**
	 * summary
	 */
	public function __construct()
	{
		$lockFile = get_class($this);
		if(!is_file(__DIR__.'/../../workers/lock/'.$lockFile)) {
			touch(__DIR__.'/../../workers/lock/'.$lockFile);
			chmod(__DIR__.'/../../workers/lock/'.$lockFile, 0755);
		}
		file_put_contents(__DIR__.'/../../workers/lock/'.$lockFile, time());
	}

	/**
	 * summary
	 */
	public function __destruct()
	{
		$lockFile = get_class($this);
		if(is_file(__DIR__.'/../../workers/lock/'.$lockFile)) {
			unlink(__DIR__.'/../../workers/lock/'.$lockFile);
		}
	}
}

?>