HTTP/1.1 200 OK

<?echo '<?xml version="1.0" encoding="utf-8"?>';?>
<device_description>
<machine_name><? echo query("/runtime/device/modelname");?></machine_name>
<machine_desc><? echo query("/runtime/device/description");?></machine_desc>
</device_description>