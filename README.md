# MemcachedAdmin Docker

<p align="center"><a href="https://github.com/phplegacy/memcachedadmin-docker"><img src="https://github.com/phplegacy/memcachedadmin-docker/raw/master/docs/interface.png"></a></p>

## Graphic stand-alone administration for MemcacheD for monitor and debug purposes

This project allows to see in **real-time** (top-like) or from the start of the server, `stats for get, set, delete, increment, decrement, evictions, reclaimed, cas command`, as well as `server stats` (network, items, server version) with Google Charts and **server internal configuration**

You can go further to **see each server slabs, occupation, memory wasted and items** (**key & value**).

Another part can execute commands to any memcached server : `get, set, delete, flush\_all` as well as execute any commands (like stats) with telnet

## Feature list

**Statistics**
 - Stats for each or all memcached servers, items, evicted, reclaimed
 - Stats for every command : set, get, delete, incr, decr, cas
 - Slabs stats (Memory, pages, memory wasted, items)
 - Items stats (View items in slabs, then data for each key)
 - Network stats (Traffic, bandwidth)

**Commands**
 - Execute commands : get, set, delete, flush_all on servers to administrate or debug it
 - Get data with key on servers
 - Delete keys on servers
 - Flush servers
 - Execute telnet command directly from phpMemcachedAdmin
 - Search for specific pattern into all keys

**Live Stats**
 - Top-like real time stats with configurable alerts

## Docker repository
[Docker Hub](https://hub.docker.com/r/legacyphp/memcachedadmin)  
`docker pull legacyphp/memcachedadmin:latest`

[GitHub Packages](https://github.com/phplegacy/memcachedadmin-docker/pkgs/container/memcachedadmin-docker)  
`docker pull ghcr.io/phplegacy/memcachedadmin-docker:latest`

## Usage

Use provided [`compose.yml`](https://github.com/phplegacy/memcachedadmin-docker/blob/master/compose.yml) as an example.

## Configuration

Use environment variables to define MemcacheD server address and port:
```sh
SERVER = 'memcached:11211'
```

Separate servers with comma in order to connect to multiple MemcacheD servers:
```sh
SERVER = 'memcached-one:11211,memcached-two:11211'
```

## Security ##

MemcachedAdmin does not provide any security system, you need to add this feature by yourself.

## Credits

- [elijaa](https://github.com/elijaa/phpmemcachedadmin) For the original implementation.
- [AlexeyPlodenko](https://github.com/AlexeyPlodenko/phpmemcachedadmin) For providing additional features.

## License

The Apache License. Please see [License File](https://github.com/phplegacy/memcachedadmin-docker/blob/master/LICENSE) for more information.

---
If you like this project, please consider giving it a ⭐
