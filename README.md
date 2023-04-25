Codeigniter 3 has an issue with CSRF validation if the request is intercepted and resubmitted, in will go though instead of failing of second/third submit.

In order to actually validate server side the value, in this example the CSRF token is stored in session and reset after the submit to prevent the above vulnerability.
Extending the Security class with session support is not possible due to how the core loading functionality is built, Session class is a library which is loaded after the Controller class.

WARNING: This is a redneck engeneering grade solution that fixes a specific scenario. Use at your own risk.

#### 1. Add another  config parameter to allow extra configuration.
In `application/config/config.php` add a new config option.
```php
... 
$config['csrf_protection'] = true;
**$config['real_csrf_protection'] = true;**
...
```

#### 2. Enable hooks
In order to enable hooks, edit  `application/config/config.php` 
```php
$config['enable_hooks'] = true;
```

#### 3. Define two new hooks:
One hook (pre_system) that will keep the initial $_POST data (at least the specific field we need), which is usually removed after Security class is loaded. We need it later on in order to take advantage of session use.

The other one (post_controller_constructor) that will validate against the session, this is is ran right after the controller construct is finished executing. At this time of execution Codeigniter has session access and can instantiate full feature instances to rely on any kind of session are used (database/files/redis).

```php
$hook['pre_system'][] = array(
  'class' => 'MY_Preserve_post',
  'function' => 'keep_csrf_fields',
  'filename' => 'MY_Preserve_post',
  'filepath' => 'hooks'
);
$hook['post_controller_constructor'][] = array(
  'class' => 'MY_Security',
  'function' => 'csrf_verify',
  'filename' => 'MY_Security.php',
  'filepath' => 'hooks'
);
```

#### 4. Add the hooks files 
Place the files from this repo in `application/hooks` folder
