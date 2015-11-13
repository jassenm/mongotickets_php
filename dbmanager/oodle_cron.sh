#!/bin/bash
cd /home/mongotickets/dbmanager
/usr/local/bin/php oodle_datafeed_creator.new.php
sleep 5
cp oodle_feed.xml oodle_feed.xml.new
gzip oodle_feed.xml
mv -f oodle_feed.xml.gz ../public_html/oodle_feed.xml.gz
