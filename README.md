# Scrollio: PHP 7 Framework

PHP 7 Application Framework utilizing Geppetto Database Object Relational Mapper.

## Notice

Scrollio is currently deployed in my production applications, so I can attest to its stability and utility. However, this repository is a work-in-progress, as I'm working to refactor the entire project and add a suite of unit tests for reliability, security and backward compatibility. **I do not recommend using this project until it reaches a stable state.**


# Running Tests with Docker

To simplify the testing process across multiple processes and system configurations, all of the tests can be run within Docker containers. You'll need to install the latest versions of Docker and Docker Compose first: [Docker website](https://www.docker.com).


To build and run, make the docker containers using the build script, and then use docker-compose to bring up the containers.

```bash
./build.sh
docker-compose up -d
```

Enter the container and run the tests using:

```bash
docker exec -it scrollio-tests /bin/bash
phpunit ./tests/
```

And when you're finished, bring down the docker containers.

```bash
docker-compose down
```