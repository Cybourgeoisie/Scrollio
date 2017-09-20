#!/bin/sh

# Build the docker images
docker build -f ./tests/Dockerfile -t scrollio-tests .