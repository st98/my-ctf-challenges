#!/bin/bash
redis-server ./redis.conf &
sleep 3
node index.js