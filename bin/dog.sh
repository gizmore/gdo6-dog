#!/usr/bin/env bash
# http://stackoverflow.com/questions/27062668/shell-script-with-a-cron-job-to-start-a-program-if-not-running/27063586#27063586
# http://stackoverflow.com/a/27063586
cd "$(dirname "$0")"
PF='./pidfile'
if kill -0 $(< "$PF") 2> /dev/null; then # process in pidfile is yours and running
	echo "Dog is already running."
    exit 0
else
    echo $$ > "$PF"
    exec ./run_dog.sh
fi
