#!/bin/bash

# Get the full path to the PHP executable
PHP_PATH=$(which php)

# Get the full path to the cron.php script
SCRIPT_PATH=$(realpath "$(dirname "$0")/cron.php")

# Add a CRON job to run cron.php every 24 hours
CRON_JOB="0 0 * * * $PHP_PATH $SCRIPT_PATH"

# Check if the CRON job already exists
(crontab -l 2>/dev/null | grep -F "$SCRIPT_PATH") && echo "CRON job already exists." && exit 0

# Add the CRON job
(crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -

echo "CRON job has been set up to run cron.php every 24 hours."