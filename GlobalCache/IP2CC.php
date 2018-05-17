<?php

namespace GlobalCache;

class IP2CC
{
	/**
	 * Global Cache's IP2CC listening socket.
	 *
	 * @var int
	 */
	const SOCKET = 4998;

	/**
	 * Socket connection timeout.
	 *
	 * @var int
	 */
	const TIMEOUT = 5;

	/**
	 * Connection resource.
	 *
	 * @var resource
	 */
	private $connection = null;

	/**
	 * Global Cache IP2CC's IP.
	 *
	 * @var string
	 */
	protected $ip_address = null;

	/**
	 * Constructor.
	 *
	 * @param  string $ip_address Global Cache's IP.
	 * @return void
	 */
	public function __construct($ip_address)
	{
		if (!preg_match('/[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$/', $ip_address))
		{
			throw new Exception('Global Cache Error :: Invalid IP Address - '.$ip_address);
		}

		$this->ip_address = $ip_address;

		return $this->connect();
	}

	/**
	 * Is connected or not.
	 *
	 * @return boolean
	 */
	private function connectionIsActive()
	{
		return (boolean) $this->connection;
	}

	/**
	 * Opens a socket connection for a iTach IP2CC.
	 *
	 * @return boolean
	 */
	private function connect()
	{
		$this->connection = fsockopen($this->ip_address, static::SOCKET, $error_no, $error_str, static::TIMEOUT);

		if ($error_no && $error_str)
		{
			throw new Exception('Global Cache Error :: '.$error_str, $error_no);
		}

		return true;
	}

	/**
	 * Sends a command through connection.
	 *
	 * @param  string $command The command.
	 * @return boolean
	 */
	private function sendCommand($command)
	{
		if (!$this->connectionIsActive())
		{
			$this->connect();
		}

		try
		{
			fwrite($this->connection, $command);
			fwrite($this->connection, "\r\n");

			fgets($this->connection, 16);
		}
		catch (Exception $e)
		{
			return false;
		}

		return true;
	}

	/**
	 * Triggers a relay by sending the setstate command.
	 *
	 * @param  string  $relay Relay.
	 * @param  boolean $state Relay state as boolean.
	 * @return boolean
	 */
	public function triggerRelay($relay, $state)
	{
		if (!preg_match('/[1]:[1-3]$/', $relay))
		{
			throw new Exception('Global Cache Error :: Invalid Relay - '.$relay);
		}
		if (!is_bool($state))
		{
			throw new Exception('Global Cache Error :: Invalid Relay State - '.$state);
		}
		$state = (int) $state;

		return $this->sendCommand("setstate,{$relay},{$state}");
	}

	/**
	 * Listen method.
	 *
	 * @param  object $callback Function callback.
	 * @return void
	 */
	public function listen($callback)
	{
		while (!feof($this->connection))
		{
			$callback(fgets($this->connection, 1024));
		}
	}

	/**
	 * Disconnects current connection.
	 *
	 * @return boolean
	 */
	public function disconnect()
	{
		return fclose($this->connection);
	}
}
