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
 * Live Stats top style
 *
 * @author Cyrille Mahieux : elijaa(at)free.fr
 *
 * @since 12/04/2010
 */

// Require
use App\Library\Command\Factory;
use App\Library\Data\Analysis;
use App\Library\Data\Errors;

require_once __DIR__.'/../../bootstrap.php';

// Initializing requests
$request = $_REQUEST['request_command'] ?? null;

// Stat of a particular cluster
if (isset($_REQUEST['cluster']) && ($_REQUEST['cluster'] != null)) {
    $cluster = $_REQUEST['cluster'];
} else {
    // Getting default cluster
    $clusters = array_keys($_ini->get('servers'));
    $cluster = $clusters[0] ?? null;
    $_REQUEST['cluster'] = $cluster;
}

// Checking writing status in temporary folder
if (is_writable($_ini->tempDirPath()) === false) {
    // Trying to change permissions
    chmod($_ini->tempDirPath(), 0775);
}

// Hashing cluster
$hash = md5($_REQUEST['cluster']);

// Cookie @FIXME not a perfect method
if (!isset($_COOKIE['live_stats_id'.$hash])) {
    // Cleaning temporary directory
    $files = glob($_ini->tempDirPath().'*', GLOB_NOSORT);
    foreach ($files as $path) {
        // Getting file last modification time
        $stats = @stat($path);

        // Deleting file older than 24 hours
        if (isset($stats['mtime']) && ($stats['mtime'] < (time() - 60 * 60 * 24))) {
            @unlink($path);
        }
    }

    // Generating unique id
    $live_stats_id = random_int(0, mt_getrandmax()).$hash;

    // Cookie
    setcookie('live_stats_id'.$hash, $live_stats_id, ['expires' => time() + 60 * 60 * 24]);
} else {
    // Backup from a previous request
    $live_stats_id = $_COOKIE['live_stats_id'.$hash];
}

// Live stats dump file
$filePath = rtrim($_ini->tempDirPath(), '/').DIRECTORY_SEPARATOR.'live_stats.'.$live_stats_id;

// Display by request type
switch ($request) {
    // Ajax ask : stats
    case 'live_stats':
        // Opening old stats dump
        $previous = @unserialize(file_get_contents($filePath));

        // Initializing variables
        $actual = [];
        $stats = [];
        $time = 0;

        // Requesting stats for each server
        foreach ($_ini->cluster($cluster) as $name => $server) {
            // Start query time calculation
            $time = microtime(true);

            // Asking server for stats
            $actual[$name] = Factory::instance('stats_api')->stats($server['hostname'], $server['port']);

            // Calculating query time length
            $actual[$name]['query_time'] = max((microtime(true) - $time) * 1000, 1);
        }

        // Analysing stats
        foreach ($_ini->cluster($cluster) as $name => $server) {
            // Making an alias @FIXME Used ?
            $server = $name;

            // Diff between old and new dump
            $stats[$server] = Analysis::diff($previous[$server], $actual[$server]);
        }

        // Making stats for each server
        foreach ($stats as $server => $array) {
            // Analysing request
            if ((isset($stats[$server]['uptime'])) && ($stats[$server]['uptime'] > 0)) {
                // Computing stats
                $stats[$server] = Analysis::stats($stats[$server]);

                // Because we make a diff on every key, we must reasign some values
                $stats[$server]['bytes_percent'] = sprintf('%.1f', $actual[$server]['bytes'] / $actual[$server]['limit_maxbytes'] * 100);
                $stats[$server]['bytes'] = $actual[$server]['bytes'];
                $stats[$server]['limit_maxbytes'] = $actual[$server]['limit_maxbytes'];
                $stats[$server]['curr_connections'] = $actual[$server]['curr_connections'];
                $stats[$server]['query_time'] = $actual[$server]['query_time'];
            }
        }

        // Saving new stats dump
        file_put_contents($filePath, serialize($actual));

        // Showing stats
        require __DIR__.'/../../view/livestats/stats.php';
        break;

        // Default : No command
    default:
        // Initializing : making stats dump
        $stats = [];
        foreach ($_ini->cluster($cluster) as $name => $server) {
            $stats[$name] = Factory::instance('stats_api')->stats($server['hostname'], $server['port']);
        }

        // Saving first stats dump
        file_put_contents($filePath, serialize($stats));

        // Searching for connection error, adding some time to refresh rate to prevent error
        $refresh_rate = max(
            $_ini->get('refresh_rate'),
            count($_ini->cluster($cluster)) * 0.25 + (Errors::count() * (0.5 + $_ini->get('connection_timeout')))
        );

        // Showing header
        require __DIR__.'/../../view/header.php';

        // Showing live stats frame
        require __DIR__.'/../../view/livestats/frame.php';

        // Showing footer
        require __DIR__.'/../../view/footer.php';

        break;
}
