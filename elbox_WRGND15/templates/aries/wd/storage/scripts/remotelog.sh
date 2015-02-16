#!/bin/sh

info=`curl -i "http://127.0.0.1:1280/api/1.0/rest/device?owner=admin&pw=" 2> /dev/null | grep remote | cut -d '>' -f2 | cut -d '<' -f1`

if [ "$info" == "true" ]; then
	xmldbc -s /runtime/remote_access/enable 1
else
	xmldbc -s /runtime/remote_access/enable 0
fi

