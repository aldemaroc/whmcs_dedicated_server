# whmcs_dedicated_server

Installing:

1. Copy "dedicado.php" to ```/modules/servers/dedicado/dedicado.php```
2. Add the following lines to librenms's config.php where 192.168.0.1 is your whmcs's IP:
```php
$config['allow_unauth_graphs_cidr'] = array('192.168.0.1/32');
$config['allow_unauth_graphs'] = true;'''
```
3. Configure the module with your librenms hostname inside your product.
4. Add a custom field named "librenmsid" to your product.

When setting up services to clients just input the librenm's port id on librenmsid field and the graph will show up.
