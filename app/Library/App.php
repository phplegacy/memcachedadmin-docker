<?php
/**
 * Copyright 2010 Cyrille Mahieux
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations
 * under the License.
 *
 * ><)))°> ><)))°> ><)))°> ><)))°> ><)))°> ><)))°> ><)))°> ><)))°> ><)))°>
 *
 * Configuration class for editing, saving, ...
 *
 * @author elijaa@free.fr
 *
 * @since 19/05/2010
 */

namespace App\Library;

class App
{
    protected const DEFAULT_PORT = 11211;

    /** @var null Singleton */
    protected static $_instance;

    /** @var array Configuration needed keys and default values */
    protected $defaultConfig = [
        'stats_api' => 'Server',
        'slabs_api' => 'Server',
        'items_api' => 'Server',
        'get_api' => 'Server',
        'set_api' => 'Server',
        'delete_api' => 'Server',
        'flush_all_api' => 'Server',
        'connection_timeout' => 1,
        'max_item_dump' => 100,
        'refresh_rate' => 2,
        'memory_alert' => 80,
        'hit_rate_alert' => 90,
        'eviction_alert' => 0,
        'servers' => [
            'Default' => [
                'memcached:11211' => [
                    'hostname' => 'memcached',
                    'port' => 11211,
                ],
            ],
        ],
    ];

    /** @var array Storage */
    protected $config;

    /** @var string */
    protected $realTempDirPath;

    /**
     * Constructor, load configuration file and parse server list
     */
    protected function __construct()
    {
        $this->config = $this->defaultConfig;
        $this->config['servers'] = $this->getServersConfig();
    }

    /**
     * Get App singleton
     */
    public static function getInstance(): self
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    private function getServersConfig(): array
    {
        $defaultServers = $this->defaultConfig['servers'];
        $serverEnv = getenv('SERVER');

        if (!$serverEnv) {
            return $defaultServers;
        }

        $serverEnv = explode(':', $serverEnv);

        $hostname = $serverEnv[0];
        $port = $serverEnv[1] ?? static::DEFAULT_PORT;

        return [
            'Default' => [
                $hostname.':'.$port => [
                    'hostname' => $hostname,
                    'port' => $port,
                ],
            ],
        ];
    }

    /**
     * Config key to retrieve
     * Return the value, or false if does not exists
     *
     * @param string $key Key to get
     *
     * @return mixed
     */
    public function get(string $key)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }

        return false;
    }

    public function tempDirPath(): string
    {
        if (!$this->realTempDirPath) {
            $this->realTempDirPath = sys_get_temp_dir();
        }

        return $this->realTempDirPath;
    }

    public function isTempDirExists(): bool
    {
        $tempDirPath = $this->tempDirPath();

        return is_dir($tempDirPath);
    }

    public function isTempDirWritable(): bool
    {
        $tempDirPath = $this->tempDirPath();

        return is_writable($tempDirPath);
    }

    /**
     * Servers to retrieve from cluster
     * Return the value, or false if does not exists
     *
     * @param string $cluster Cluster to retrieve
     */
    public function cluster(string $cluster): array
    {
        if (isset($this->config['servers'][$cluster])) {
            return $this->config['servers'][$cluster];
        }

        return [];
    }

    /**
     * Check and return server data
     * Return the value, or false if does not exists
     *
     * @param string $server Server to retrieve
     */
    public function server(string $server): array
    {
        foreach ($this->config['servers'] as $cluster => $servers) {
            if (isset($this->config['servers'][$cluster][$server])) {
                return $this->config['servers'][$cluster][$server];
            }
        }

        return [];
    }

    /**
     * Config key to set
     *
     * @param string $key   Key to set
     * @param mixed  $value Value to set
     */
    public function set(string $key, $value)
    {
        $this->config[$key] = $value;
    }
}
